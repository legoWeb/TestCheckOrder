<?php

namespace Polushkin\CheckOrderNewCustomer\Block\Order;


class View extends \Magento\Framework\View\Element\Template
{
    protected $view;
    protected $orderRepository;
    protected $searchCriteriaBuilder;
    protected $_orderCollectionFactory;
    public $c = [];

    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    protected $orders;


    /**
     * @var string
     */
    protected $_template = 'Polushkin_CheckOrderNewCustomer::check_order.phtml';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Block\Order\View $view,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->view = $view;
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_orderCollectionFactory = $orderCollectionFactory;
    }

    public function getOrderById($id)
    {
        return $this->orderRepository->get($id);
    }

    public function getOrderByIncrementId($incrementId)
    {
        $this->searchCriteriaBuilder->addFilter('increment_id', $incrementId);

        $order = $this->orderRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        return $order;
    }

    public function getOrders()
    {
        if (!$this->orders) {
            $this->orders = $this->_orderCollectionFactory->create()->addFieldToSelect('*');
        }
        return $this->orders;
    }

    public function test()
    {

        $result = [];
        $a = $this->_orderCollectionFactory->create()->getData();
        foreach ($a as $b) {
            $result[] = [
                "entity_id" => $b['entity_id'],
                "increment_id" => $b["increment_id"],
                "protect_code" => $b['protect_code']
            ];
        }
        return $result[0];
    }

    public function createMyUrl()
    {
        $data = $this->test();
        $securityKey = $data["increment_id"].'-'.$data["entity_id"].'-'.$data["protect_code"];
        $generateSecurityKey = substr(md5($securityKey), 1, 7);
        return $this->getUrl('checkorder/');
//            ."/checkorder/order/" .$data["increment_id"] ."/key/". $generateSecurityKey;
    }
}