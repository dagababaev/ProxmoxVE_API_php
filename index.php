<?php
// ------------------------------------------------------------------------------
//  © Copyright (с) 2020
//  Author: Dmitri Agababaev, d.agababaev@duncat.net
//
//  Redistributions and use of source code, with or without modification, are
//  permitted that retain the above copyright notice
//
//  License: MIT
// ------------------------------------------------------------------------------

require_once 'lib/ProxmoxVE_API.class.php';

$param = [
    'hostname' => 'pve.host.com', // required (domain name or IPv4)
    'username' => 'username', // required
    'password' => 'password', // required
    'realm' => 'pam', // pam or pve auth type
    'port' => 8006, // if the port is changed. optional
    'ssl' => false // not required if false. optional
];

$pve = new ProxmoxVE_API($param);

$node = 'pve-01'; // node name
$vmid = '200'; // VM uniq ID

if ($pve->login()) {

    // SAMPLES

    // Without parameters
    // GET
    $result = $pve->get("/cluster/nextid");

    // POST
    # $result = $pve->post("/nodes/{$node}/qemu/{$vmid}/status/start");
    // PUT
    # $result = $pve->put("/nodes/{$node}/qemu/{$vmid}/config");
    // DELETE
    # $snapname = 'vm_snapname';
    # $result = $pve->delete("/nodes/{$node}/qemu/{$vmid}/snapshot/{$snapname}");


    // With parameters
    // GET
    # $result = $pve->get("/nodes/{$node}/qemu/{$vmid}/rrddata", [
    #    'timeframe' => 'hour'
    # ]);

    // POST
    # $result = $pve->post("/nodes/{$node}/qemu/{$vmid}/status/shutdown", [
    #   'timeout' => '20'
    # ]);

    // PUT
    # $result = $pve->put("/nodes/{$node}/qemu/{$vmid}/config", [
    #     'name' => 'newname'
    # ]);

    // DELETE
    # $result = $pve->delete("/nodes/{$node}/qemu/{$vmid}", [
    #    'purge' => true,
    #    'skiplock' => true
    # ]);

    var_dump($result);

    // noVNC
    # echo '<iframe src="'.$pve->noVNC($node, $vmid).'" frameborder="0" scrolling="no" width="100%" height="100%"></iframe>';

}
