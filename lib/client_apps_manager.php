<?php

/*
 * This file is part of the AppManager package.
 *
 * (c) Your Name <your.email@example.com>
 */
require_once __DIR__ . '/logger.php';

class ClientAppsManager
{

    public function getClientsByUuid($uuid)
    {
        $clientsFile = __DIR__ . '/../conf/clients.json';
        try {
           
            if (file_exists($clientsFile)) {
                //retrieve clients configuration from json file:
                $string = file_get_contents($clientsFile);
                $clientList = json_decode($string, true);

                if (json_last_error() !== JSON_ERROR_NONE || !is_array($clientList)) {
                    Logger::safeError("Error parsing clients configuration JSON file.", array('error' => json_last_error_msg()));
                    throw new Exception("Error parsing clients configuration JSON file: " . json_last_error_msg());
                }else {
                    foreach($clientList as $clientApp) {
                        if (isset($clientApp['uuid']) && $clientApp['uuid'] === $uuid) {
                            return $clientApp;
                        }
                    }
                }
            } else {
                Logger::safeError("Clients configuration file not found: $clientsFile");
                throw new Exception("Clients configuration file not found: $clientsFile");
            }
        } catch (Exception $e) {
            Logger::safeError("Failed to load clients configuration.", array('exception' => $e->getMessage()));
            throw new Exception("Failed to load clients configuration: " . $e->getMessage());
        }
    }
} 