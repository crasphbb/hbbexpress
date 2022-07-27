<?php
/**
 * Kuaidi100Gateway.php
 * @author huangbinbin
 * @date   2022/7/27 16:23
 */

namespace Crasp\Hbbexpress\Gateways;


use Crasp\Hbbexpress\Exceptions\InvailArgumentException;
use Overtrue\Http\Client;

class Kuaidi100Gateway extends Gateway
{
    private $url = 'https://poll.kuaidi100.com/poll/query.do';


    /**
     * @param string $trackNuber
     * @param string $company
     *
     * @return array
     * @throws InvailArgumentException
     * @author huangbinbin
     * @date   2022/7/27 16:51
     */
    public function query(string $trackNuber,string $company = '')
    {
        $com = $this->getCompany($company);
        $postJson = \json_encode([
            'num' => $trackNuber,
            'com' => $com,
        ]);
        $postData = [
            'customer' => $this->config['app_secret'],
            'sign' => \strtoupper(\md5($postJson  . $this->config['app_key']. $this->config['app_secret'])),
            'param' => $postJson
        ];
        $response = $this->clienHttp->post($this->url,$postData,[
            'Content-Type' => 'application/x-www-form-urlencoded'
        ]);
        return $this->format($response);
    }

    /**
     * 格式化
     * @param array $detail
     *
     * @return array
     * @author huangbinbin
     * @date   2022/7/27 17:01
     */
    public function format(array $detail)
    {
        if(isset($detail['status']) && $detail['status'] == 200) {
            return [
                'code' => self::SUCESS_CODE,
                'message' => $detail['message'],
                'data' => $detail
            ];
        }else{
            return [
                'code' => self::FAIL_CODE,
                'message' => $detail['message'],
                'data' => $detail
            ];
        }
    }
}