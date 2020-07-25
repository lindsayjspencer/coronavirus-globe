<?php

/*
 * Endpoint for Github Webhook URLs
 *
 * see: https://help.github.com/articles/post-receive-hooks
 *
 */

// script errors will be send to this email:
$error_mail = "lindsayjspencer@gmail.com";

function run()
{
        global $rawInput;

        // read config.json
        $config_filename = 'config.json';
        if (!file_exists($config_filename)) {
                throw new Exception("Can't find " . $config_filename);
        }
        $config = json_decode(file_get_contents($config_filename), true);

        $postBody = $_POST['payload'];
        $payload = json_decode($postBody);

        // check if the request comes from github server
        $output = 'Output:/n';
        foreach ($config['endpoints'] as $endpoint) {
                // check if the push came from the right repository and branch
                if (
                        $payload->repository->url == 'https://github.com/' . $endpoint['repo']
                        && $payload->ref == 'refs/heads/' . $endpoint['branch']
                ) {

                        // execute update script, and record its output
                        $output = shell_exec($endpoint['run']);
                }
        }
        return $output;
}
if (!isset($_POST['payload'])) {
        echo "Works fine.";
} else {
        echo run();
}
