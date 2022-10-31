<?php
/**
 * Kuaidi100Gateway.php
 * @author huangbinbin
 * @date   2022/7/27 16:23
 */

namespace Crasp\Hbbexpress\Gateways;


use Crasp\Hbbexpress\Exceptions\InvailArgumentException;

class Kuaidi100Gateway extends Gateway
{
    //查询url
    private $url = 'https://poll.kuaidi100.com/poll/query.do';
    //注册url
    private $registerUrl = 'https://poll.kuaidi100.com/poll';


    /**
     * @param string $trackNumber
     * @param string $company
     *
     * @return array
     * @throws InvailArgumentException
     * @author huangbinbin
     * @date   2022/7/27 16:51
     */
    public function query(string $trackNumber, string $company = ''): array
    {
        $isCompany = $this->config['is_company'] ?: false;
        $com = $this->getCompany($company, $isCompany);
        $postJson = \json_encode([
            'num' => $trackNumber,
            'com' => $com,
        ]);
        $postData = [
            'customer' => $this->config['app_secret'],
            'sign'     => \strtoupper(\md5($postJson . $this->config['app_key'] . $this->config['app_secret'])),
            'param'    => $postJson,
        ];
        $response = $this->clienHttp->post($this->url, $postData, [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);

        return $this->format($response);
    }

    /**
     * @param string $trackNumber
     * @param string $company
     *
     * @return array
     * @throws InvailArgumentException
     * @author huangbinbin
     * @date   2022/10/17 16:31
     */
    public function register(string $trackNumber, string $company = ''): array
    {
        if (!$this->config['callbackurl']) {
            throw new InvailArgumentException('回调地址不能为空');
        }
        $isCompany = $this->config['is_company'] ?: false;
        $com = $this->getCompany($company, $isCompany);
        $postJson = \json_encode([
            'company'    => $com,
            'number'     => $trackNumber,
            'key'        => $this->config['app_key'],
            'parameters' => [
                'callbackurl' => $this->config['callbackurl'],
            ],
        ]);
        $postData = [
            'schema' => 'json',
            'param'  => $postJson,
        ];
        $response = $this->clienHttp->post($this->registerUrl, $postData, [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);

        return $this->format($response);
    }

    /**
     * 格式化
     *
     * @param array $detail
     *
     * @return array
     * @author huangbinbin
     * @date   2022/7/27 17:01
     */
    public function format(array $detail): array
    {
        if (isset($detail['status']) && $detail['status'] == 200) {
            return [
                'code'    => self::SUCESS_CODE,
                'message' => $detail['message'],
                'data'    => $detail,
            ];
        } else {
            return [
                'code'    => self::FAIL_CODE,
                'message' => $detail['message'],
                'data'    => $detail,
            ];
        }
    }
}
