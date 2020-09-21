<?php
/**
 * Created by PhpStorm.
 * User: agdsign
 * Date: 21.09.2020
 * Time: 18:44
 */
class PVE_Exception extends RuntimeException {}

class ProxmoxVE_API
{
    protected $hostname;
    protected $username;
    protected $password;
    protected $realm;
    protected $port;
    protected $ssl;

    protected $login_data = [];

    public $ticket = null;
    protected $CSRFPToken = null;

    public function __construct ($param) {
        $this->hostname = filter_var($param['hostname'], FILTER_VALIDATE_IP) ? gethostbyname($param['hostname']) : $param['hostname'];
        $this->hostname = $param['hostname'];
        $this->username = $param['username'];
        $this->password = $param['password'];
        $this->realm = $param['realm'];

        $this->port = (isset($param['port']) && is_int($param['port'])) ? $param['port'] : 8006;
        $this->ssl = (isset($param['ssl']) && is_bool($param['ssl'])) ? $param['ssl'] : false;

    }

    public function login() {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://{$this->hostname}:{$this->port}/api2/json/access/ticket");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['username' => $this->username,
                                                                      'password' => $this->password,
                                                                      'realm' => $this->realm
                                                                    ]));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl);
        $response = curl_exec($ch);
        $login_response = curl_getinfo($ch);
        curl_close($ch);

        if ($login_response['ssl_verify_result'] == 1) {
            throw new PVE_Exception("Incorrect SSL on {$this->hostname}", 4);
        }

        $this->login_data = (json_decode($response, true))['data'];
        $this->ticket = $this->login_data['ticket'];
        $this->CSRFPToken = $this->login_data['CSRFPreventionToken'];

        return true;
    }

    private function CURL($api_http, $method, $param = NULL) {

        if (isset($param)){
            $param = http_build_query($param);
            if ($method == 'GET' || $method == 'DELETE' || $method == 'PUT') $get_param = '/?'.$param;
        } else {
            $get_param = NULL;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://{$this->hostname}:{$this->port}/api2/json{$api_http}{$get_param}");

        switch ($method) {
            case "POST":
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
                break;
            case "PUT":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                break;
            case "DELETE":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            default:
                return false;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, ["CSRFPreventionToken:{$this->CSRFPToken}"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, "PVEAuthCookie={$this->ticket}");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $response = (json_decode($response, true))['data'];
        return $response;
    }

    public function get($api_http, $param = null) {
        return $this->CURL($api_http, 'GET', $param);
    }
    public function post($api_http, $param = null) {
        return $this->CURL($api_http, 'POST', $param);
    }
    public function put($api_http, $param = null) {
        return $this->CURL($api_http, 'PUT', $param);
    }
    public function delete($api_http, $param = null) {
        return $this->CURL($api_http, 'DELETE', $param);
    }

    public function noVNC($node, $vmid) {
        $conf = $this->post("/nodes/{$node}/qemu/{$vmid}/vncproxy", [
            'websocket' => 1
        ]);
        $websock = $this->get("/nodes/{$node}/qemu/{$vmid}/vncwebsocket", [
            'vncticket' => $this->ticket,
            'port'      => $conf['port']
        ]);
        setcookie('PVEAuthCookie', $this->ticket , 0, '/', $_SERVER['HTTP_HOST'], false);
        $src = "https://{$this->hostname}:8006/?console=kvm&novnc=1&node={$node}&resize=scale&vmid={$vmid}&path=api2/json";
        $src .= "/nodes/{$node}/qemu/{$vmid}/vncwebsocket/port/".$conf['port'];

        return $src;
    }
}