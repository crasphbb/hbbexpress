<?php
/**
 * Express.php
 * @author huangbinbin
 * @date   2022/7/27 16:19
 */

namespace Crasp\Hbbexpress;


use Crasp\Hbbexpress\Exceptions\InvailArgumentException;

class Express
{
    private $gateway;
    /**
     * @var array
     * @author huangbinbin
     * @date   2022/7/27 16:35
     */
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * 查詢方法
     *
     * @param string $trackNumber
     * @param string $type
     * @param string $company
     * @param string $phone
     *
     * @return mixed
     * @throws InvailArgumentException
     * @author huangbinbin
     * @date   2022/7/27 16:35
     */
    public function query(string $trackNumber = '', string $type = 'kuaid-100', string $company = '', string $phone = '')
    {
        if (!$trackNumber) {
            throw new InvailArgumentException('运单号不能为空');
        }
        $gateway = $this->getGateway($type);

        return $gateway->query($trackNumber, $company, $phone);

    }

    /**
     * 注册
     *
     * @param string $trackNumber
     * @param string $type
     * @param string $company
     * @param string $phone
     *
     * @return mixed
     * @throws InvailArgumentException
     * @author huangbinbin
     * @date   2022/11/1 17:27
     */
    public function register(string $trackNumber = '', string $type = 'kuaid-100', string $company = '', string $phone = '')
    {
        if (!$trackNumber) {
            throw new InvailArgumentException('运单号不能为空');
        }
        $gateway = $this->getGateway($type);

        return $gateway->register($trackNumber, $company, $phone);

    }

    /**
     * @param string $type
     *
     * @return mixed
     * @throws InvailArgumentException
     * @author huangbinbin
     * @date   2022/7/27 16:35
     */
    public function getGateway(string $type)
    {
        if (!isset($this->geteway[$type])) {
            if (!$type) {
                throw new InvailArgumentException("type参数不能为空");
            }
            $gatewayName = $this->getGatewayName($type);
            if (!\class_exists($gatewayName)) {
                throw new InvailArgumentException('当前类型不可用');
            }
            $this->gateway[$type] = new $gatewayName($this->config);
        }

        return $this->gateway[$type];
    }

    /**
     * 獲取類名
     *
     * @param string $type
     *
     * @return string
     * @author huangbinbin
     * @date   2022/7/27 16:28
     */
    public function getGatewayName(string $type)
    {
        return __NAMESPACE__ . '\\Gateways\\' . \ucfirst(\str_replace(['-', '_', ' '], '', $type)) . 'Gateway';

    }
}
