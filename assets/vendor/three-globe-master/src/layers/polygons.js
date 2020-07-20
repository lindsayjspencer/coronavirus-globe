import {
  DoubleSide,
  Group,
  Line,
  LineBasicMaterial,
  Mesh,
  MeshLambertMaterial
} from 'three';

const THREE = window.THREE
  ? window.THREE // Prefer consumption from global THREE, if exists
  : {
  DoubleSide,
  Group,
  Line,
  LineBasicMaterial,
  Mesh,
  MeshLambertMaterial
};

import { ConicPolygonBufferGeometry } from 'three-conic-polygon-geometry';
import { GeoJsonGeometry } from 'three-geojson-geometry';

import Kapsule from 'kapsule';
import accessorFn from 'accessor-fn';
import TWEEN from '@tweenjs/tween.js';

import { colorStr2Hex, colorAlpha } from '../utils/color-utils';
import { emptyObject } from '../utils/gc';
import threeDigest from '../utils/digest';
import { GLOBE_RADIUS } from '../constants';

//

export default Kapsule({
  props: {
    polygonsData: { default: [] },
    polygonGeoJsonGeometry: { default: 'geometry' },
    polygonSideColor: { default: () => '#ffffaa' },
    polygonCapColor: { default: () => '#ffffaa' },
    polygonStrokeColor: {},
    polygonAltitude: { default: 0.1 }, // in units of globe radius
    polygonsTransitionDuration: { default: 1000, triggerUpdate: false } // ms
  },

  init(threeObj, state) {
    // Clear the scene
    emptyObject(threeObj);

    // Main three object to manipulate
    state.scene = threeObj;
  },

  update(state) {
    // Data accessors
    const geoJsonAccessor = accessorFn(state.polygonGeoJsonGeometry);
    const altitudeAccessor = accessorFn(state.polygonAltitude);
    const capColorAccessor = accessorFn(state.polygonCapColor);
    const sideColorAccessor = accessorFn(state.polygonSideColor);
    const strokeColorAccessor = accessorFn(state.polygonStrokeColor);

    const singlePolygons = [];
    state.polygonsData.forEach(polygon => {
      const objAttrs = {
        data: polygon,
        capColor: capColorAccessor(polygon),
        sideColor: sideColorAccessor(polygon),
        strokeColor: strokeColorAccessor(polygon),
        altitude: +altitudeAccessor(polygon)
      };

      const geoJson = geoJsonAccessor(polygon);
      const geoId = polygon.__id || `${Math.round(Math.random() * 1e9)}`; // generate and stamp polygon ids to keep track in digest
      polygon.__id = geoId;

      if (geoJson.type === 'Polygon') {
        singlePolygons.push({
          id: `${geoId}_0`,
          coords: geoJson.coordinates,
          ...objAttrs
        });
      } else if (geoJson.type === 'MultiPolygon') {
        singlePolygons.push(...geoJson.coordinates.map((coords, idx) => ({
          id: `${geoId}_${idx}`,
          coords,
          ...objAttrs
        })));
      } else {
        console.warn(`Unsupported GeoJson geometry type: ${geoJson.type}. Skipping geometry...`);
      }
    });

    threeDigest(singlePolygons, state.scene, {
      idAccessor: d => d.id,
      createObj: () => {
        const obj = new THREE.Group();

        // conic geometry
        obj.add(new THREE.Mesh(
          undefined,
          [
            new THREE.MeshLambertMaterial({ side: THREE.DoubleSide, depthWrite: true }), // side material
            new THREE.MeshLambertMaterial({ side: THREE.DoubleSide, depthWrite: true }) // cap material
          ]
        ));

        // polygon stroke
        obj.add(new THREE.Line(
          undefined,
          new THREE.LineBasicMaterial()
        ));

        obj.__globeObjType = 'polygon'; // Add object type

        return obj;
      },
      updateObj: (obj, { coords, capColor, sideColor, strokeColor, altitude }) => {
        const [conicObj, strokeObj] = obj.children;

        // hide stroke if no color set
        const addStroke = !!strokeColor;
        strokeObj.visible = addStroke;

        // update materials
        [sideColor, capColor].forEach((color, materialIdx) => {
          // conic object
          const material = conicObj.material[materialIdx];
          const opacity = colorAlpha(color);
          material.color.set(colorStr2Hex(color));
          material.transparent = opacity < 1;
          material.opacity = opacity;
        });

        if (addStroke) {
          // stroke object
          const material = strokeObj.material;
          const opacity = colorAlpha(strokeColor);
          material.color.set(colorStr2Hex(strokeColor));
          material.transparent = opacity < 1;
          material.opacity = opacity;
        }

        const geoJsonGeometry = {
          type: 'Polygon',
          coordinates: coords
        };

        const applyUpdate = td => {
          const { alt } = obj.__currentTargetD = td;

          conicObj.geometry = new ConicPolygonBufferGeometry(coords, GLOBE_RADIUS, GLOBE_RADIUS * (1 + alt), false);
          addStroke && (strokeObj.geometry = new GeoJsonGeometry(geoJsonGeometry, GLOBE_RADIUS  * (1 + alt + 1e-4))); // stroke slightly above the conic mesh
        };

        const targetD = { alt: altitude };
        const currentTargetD = obj.__currentTargetD || { alt: -1e-3 };

        if (Object.keys(targetD).some(k => currentTargetD[k] !== targetD[k])) {
          if (!state.polygonsTransitionDuration || state.polygonsTransitionDuration < 0) {
            // set final position
            applyUpdate(targetD);
          } else {
            // animate
            new TWEEN.Tween(currentTargetD)
              .to(targetD, state.polygonsTransitionDuration)
              .easing(TWEEN.Easing.Quadratic.InOut)
              .onUpdate(applyUpdate)
              .start();
          }
        }
      }
    });
  }
});
