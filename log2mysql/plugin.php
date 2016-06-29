<?php
/**
 * Plugin log2mysql for phpMussel.
 *
 * PLUGIN INFORMATION BEGIN
 *         Plugin Name: log2mysql.
 *       Plugin Author: Michael Trefzer.
 *      Plugin Version: 1.0.0
 *    Download Address: https://github.com/mtrefzer/plugin-log2mysql
 *     Min. Compatible: 1.0.0-DEV
 *     Max. Compatible: -
 *        Tested up to: 1.0.0-DEV
 *       Last Modified: 2016.06.29
 * PLUGIN INFORMATION END
 *
 * This plugin can be used to write the logging information to a mysql database.
 */
/**
 * Prevents direct access (the plugin should only be called from the phpMussel
 * plugin system).
 */
if (!defined('phpMussel')) {
    die('[phpMussel] This should not be accessed directly.');
}
/** Fallback for missing "log2mysql" configuration category. */
if (!isset($phpMussel['Config']['log2mysql'])) {
    $phpMussel['Config']['log2mysql'] = array();
}
/** Fallback for missing "db_host" configuration directive. */
if (!isset($phpMussel['Config']['log2mysql']['db_host'])) {
    $phpMussel['Config']['log2mysql']['db_host'] = '';
}
/** Fallback for missing "db_port" configuration directive. */
if (!isset($phpMussel['Config']['log2mysql']['db_port'])) {
    $phpMussel['Config']['log2mysql']['db_port'] = '3306';
}
/** Fallback for missing "db_name" configuration directive. */
if (!isset($phpMussel['Config']['log2mysql']['db_name'])) {
    $phpMussel['Config']['log2mysql']['db_name'] = '';
}
/** Fallback for missing "db_user" configuration directive. */
if (!isset($phpMussel['Config']['log2mysql']['db_user'])) {
    $phpMussel['Config']['log2mysql']['db_user'] = '';
}
/** Fallback for missing "db_pass" configuration directive. */
if (!isset($phpMussel['Config']['log2mysql']['db_pass'])) {
    $phpMussel['Config']['log2mysql']['db_pass'] = '';
}
/** Fallback for missing "db_table" configuration directive. */
if (!isset($phpMussel['Config']['log2mysql']['db_table'])) {
    $phpMussel['Config']['log2mysql']['db_table'] = '';
}

/**
 * Registers the `$phpMussel_log2mysql` closure to the `before_html_out`
 * hook.
 */
$phpMussel['Register_Hook']('phpMussel_log2mysql', 'before_html_out');

/**
 * @return bool Returns true if everything is working correctly.
 */
$phpMussel_log2mysql = function () use (&$phpMussel) {
    if (empty($phpMussel['Config']['log2mysql']['db_host']) ||
        empty($phpMussel['Config']['log2mysql']['db_name']) ||
        empty($phpMussel['Config']['log2mysql']['db_user']) ||
        empty($phpMussel['Config']['log2mysql']['db_pass']) ||
        empty($phpMussel['Config']['log2mysql']['db_table']) ||
        empty($phpMussel['whyflagged'])
    ) {
        return false;
    }
    try {
        $con = new PDO("mysql:host="
            . $phpMussel['Config']['log2mysql']['db_host']
            . ";port="
            . $phpMussel['Config']['log2mysql']['db_port']
            . ";dbname="
            . $phpMussel['Config']['log2mysql']['db_name'],
            $phpMussel['Config']['log2mysql']['db_user'],
            $phpMussel['Config']['log2mysql']['db_pass'],
            array(PDO::ATTR_PERSISTENT => true));
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $params = array('origin'           => $_SERVER[$phpMussel['Config']['general']['ipaddr']],
                        'objects_scanned'  => $phpMussel['memCache']['objects_scanned'],
                        'detections_count' => $phpMussel['memCache']['detections_count'],
                        'scan_errors'      => $phpMussel['memCache']['scan_errors'],
                        'killdata'         => $phpMussel['killdata'],
                        'detections'       => trim($phpMussel['whyflagged']));

        $query = "INSERT INTO "
            . $phpMussel['Config']['log2mysql']['db_table']
            . " (" . implode(",", array_keys($params))
            . ") VALUES (:"
            . implode(",:", array_keys($params)) . ")";
        
        $stmt = $con->prepare($query);
        $stmt->execute($params);
        if (!$stmt) {
            return false;
        }
    } catch (PDOException $e) {
        die('[phpMussel_log2mysql] Check your database configuration. -- ' . $e->getMessage());
    }
    return true;
};
