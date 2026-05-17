<?php


/* * This file defines the DaoTools class, which can be used to implement common tools for Data Access Objects (DAOs).
 * Currently, it's just a placeholder for future development.
 */
class DaoTools
{
    
    static function checkAppAccess($daoList) {
        $allowedDaoList = getenv('DAO_LIST');
        if ($allowedDaoList !== false) {
            $allowedDaos = array_map('trim', explode(',', $allowedDaoList));
            foreach ($daoList as $dao) {
                if (!in_array($dao, $allowedDaos)) {
                    Logger::debug("Access denied for DAO: $dao. Not in allowed DAO list.");
                    return false;
                }
            }
            return true;
        } else {
            Logger::debug("DAO_LIST environment variable is not set.");
            return false;
        }

    }
}