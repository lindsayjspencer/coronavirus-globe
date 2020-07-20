const csv = require('csv-parser');
const http = require('https');
const fs = require('fs');

const tempFile = "/var/www/globe/data/_data.csv";
const saveFile = "/var/www/globe/data/data.csv";

const file = fs.createWriteStream(tempFile);
const request = http.get("https://raw.githubusercontent.com/owid/covid-19-data/master/public/data/owid-covid-data.csv", function(response) {
  response.pipe(file);
  console.log('CSV file successfully downloaded');

  file.on('finish', function() {
    file.close(cb);  // close() is async, call cb after close completes.
  });

  var cb = function() {
      var tempStats = fs.statSync(tempFile)
      var tempSize = tempStats["size"]

      try {
        if (fs.existsSync(saveFile)) {
          //file exists
          var saveStats = fs.statSync(saveFile)
          var saveSize = saveStats["size"]
        } else {
              fs.closeSync( fs.openSync(saveFile, 'w') )
              var saveSize = 0;
        }
      } catch(err) {
        // console.error(err)
        console.log("file does not exist");
      }

      console.log("temp file size: "+tempSize);
      console.log("save file size: "+saveSize);

      if(tempSize>saveSize) {
          fs.copyFile(tempFile, saveFile, (err) => {
              if (err) throw err;
              console.log('Data updated');
              processFile();
          });
      }

      var processFile = function() {

          const countries = []

          function countryExists(country) {
              var exists = false;
              countries.forEach((c) => {
                  if(c.name == country.name) {
                      exists = c;
                  }
              });
              return exists;
          }


          const csvdata = [];

          fs.createReadStream('/var/www/globe/data/data.csv')
            .pipe(csv())
            .on('data', (row) => {
              var country = {
                  name: row.location,
                  dates: []
              };
              if(!countryExists(country)) {
                  countries.push( country );
              } else {
                  country = countryExists(country);
              }
              country.dates.push( row );
            })
            .on('end', () => {
              console.log('CSV file processed');
              countries.forEach((c) => {

                  var death_pm = parseFloat(c.dates[c.dates.length - 1].total_deaths_per_million) || 0;
                  var death_t = parseFloat(c.dates[c.dates.length - 1].total_deaths) || 0;
                  var cases_pm = parseFloat(c.dates[c.dates.length - 1].total_cases_per_million) || 0;
                  var cases_t = parseFloat(c.dates[c.dates.length - 1].total_cases) || 0;
                  var tests_pt = parseFloat(c.dates[c.dates.length - 1].total_tests_per_thousand) || 0;
                  var tests_t = parseFloat(c.dates[c.dates.length - 1].total_tests) || 0;

                  c.dates.forEach((d) => {
                      if(d.total_tests_per_thousand>tests_pt) { tests_pt = d.total_tests_per_thousand; }
                      if(d.total_tests>tests_t) { tests_t = d.total_tests; }
                  });

                  var country = {
                      location: c.name,
                      death_pm: death_pm,
                      death_t: death_t,
                      cases_pm: cases_pm,
                      cases_t: cases_t,
                      tests_pt: tests_pt,
                      tests_t: tests_t
                  }

                  csvdata.push( country );

              });
              const createCsvWriter = require('csv-writer').createObjectCsvWriter;
              const csvWriter = createCsvWriter({
                path: '/var/www/globe/data/coronavirus-data.csv',
                header: [
                  {id: 'location', title: 'location'},
                  {id: 'death_pm', title: 'death_pm'},
                  {id: 'death_t', title: 'death_t'},
                  {id: 'cases_pm', title: 'cases_pm'},
                  {id: 'cases_t', title: 'cases_t'},
                  {id: 'tests_pt', title: 'tests_pt'},
                  {id: 'tests_t', title: 'tests_t'}
                ]
              });

              csvWriter
                .writeRecords(csvdata)
                .then(()=> console.log('Formatted to coronavirus-data.csv'));
            });

        }

    }

});
