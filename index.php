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
    'hostname' => 'pve.host.com',
    'username' => 'username',
    'password' => 'password',
    'realm' => 'pam', // pam or pve
    'port' => 8006,
    'ssl' => false
];

$pve = new ProxmoxVE_API($param);

if ($pve->login()) {

    $node = 'pve-01';
    $vmid = '200';

    // SAMPLES

    // Without parameters
    // GET
    # $result = $pve->get("/cluster/nextid");
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
    # echo '<iframe src="'.$pve->noVNC('pve-01', 100).'" frameborder="0" scrolling="no" width="100%" height="100%"></iframe>';

}
