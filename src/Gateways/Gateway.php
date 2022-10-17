<?php
/**
 * Gateway.php
 * @author huangbinbin
 * @date   2022/7/27 17:10
 */

namespace Crasp\Hbbexpress\Gateways;


use Crasp\Hbbexpress\Exceptions\InvailArgumentException;
use Overtrue\Http\Client;

abstract class Gateway
{
    const SUCESS_CODE = 200;
    const FAIL_CODE = 500;
    const COMPANY_RESOURCE = __DIR__ . '/../Resources/company.php';
    /**
     * @var mixed
     * @author huangbinbin
     * @date   2022/7/27 16:41
     */
    protected mixed $config;
    /**
     * @var Client
     * @author huangbinbin
     * @date   2022/7/27 16:46
     */
    protected Client $clienHttp;

    /**
     * KuaidiniaoGateway constructor.
     *
     * @param array $config
     *
     * @throws InvailArgumentException
     */
    public function __construct(array $config)
    {
        $this->config = $this->getConfig($config);
        $this->clienHttp = new Client();
    }

    /**
     * @param array $config
     *
     * @return mixed
     * @throws InvailArgumentException
     * @author huangbinbin
     * @date   2022/7/27 17:10
     */
    public function getConfig(array $config): mixed
    {
        $className = $this->getClassName();
        if (!isset($config[$className])) {
            throw new InvailArgumentException('快遞100配置不存在');
        }

        return $config[$className];
    }

    /**
     *
     * @return string
     * @author huangbinbin
     * @date   2022/7/27 17:09
     */
    public function getClassName(): string
    {
        return \strtolower(\str_replace([__NAMESPACE__, '\\', 'Gateway'], '', \get_class($this)));
    }

    /**
     * @param string $company
     *
     * @return mixed
     * @throws InvailArgumentException
     * @author huangbinbin
     * @date   2022/7/27 17:07
     */
    public function getCompany(string $company): mixed
    {
        $companyResource = require self::COMPANY_RESOURCE;
        $className = $this->getClassName();
        if (!isset($companyResource[$company][$className])) {
            throw new InvailArgumentException('當前渠道不支持');
        }

        return $companyResource[$company][$className];
    }

    /***
     * @param string $trackNumber
     * @param string $company
     *
     * @return mixed
     * @author huangbinbin
     * @date   2022/7/27 17:13
     */
    abstract public function query(string $trackNumber, string $company = ''): array;

    /**
     * @param string $trackNumber
     * @param string $company
     * @param string $callbackurl
     *
     * @return mixed
     * @author huangbinbin
     * @date   2022/10/17 16:32
     */
    abstract public function register(string $trackNumber, string $company = ''): array;

    /**
     * @param array $detail
     *
     * @return mixed
     * @author huangbinbin
     * @date   2022/7/27 17:13
     */
    abstract public function format(array $detail): array;

}
