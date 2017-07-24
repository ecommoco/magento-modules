<?php
namespace Ecommoco\Paylike\Model\Config\Source;
class Paymentmode implements \Magento\Framework\Option\ArrayInterface
{    
    public function toOptionArray()
    {
        return [
            ['value' => 'authorize', 'label' => __('Authorize Only')],
            ['value' => 'authorize_capture', 'label' => __('Authorize and Capture')],           
        ];
    }
}
 
?>