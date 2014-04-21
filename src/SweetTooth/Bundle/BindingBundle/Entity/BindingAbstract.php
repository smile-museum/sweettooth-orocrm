<?php

namespace SweetTooth\Bundle\BindingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SweetTooth_Error;

/**
 * ContactBinding
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class BindingAbstract
{
    /**
     * Instance variable to cache the remote object
     * after it's been requested from ST
     * @var array
     */
    protected $_remoteObject;

    /**
     * Update created and updated at values
     *
     * TODO: Abstract this
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps()
    {
        // TODO: There's gotta be a better way of setting defaults..
        if ($this->getInSync() == null) {
            $this->setInSync(false);
        }

        $this->setUpdatedAt(new \DateTime());

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime());
        }
    }

    /**
     * Sets the binding in an error state and bumps the retry count.
     * The binding will be retried next cron run.
     *
     * @throws Exception $e
     * @param Exception $e
     */
    public function addException($e)
    {
        // We only want to catch our own exceptions, rethrow otherwise
        if (!($e instanceof SweetTooth_Error)) {
            throw $e;
        }

        $exceptionType = get_class($e);

        $this->setInSync(false)
            ->setInErrorState(true)
            ->setErrorType($exceptionType)
            ->setErrorMessage($e->getMessage())
            ->bumpRetryCount();

        /**
         * There are a few cases where we want to set the in_sync flag as true even while we're in an error state.
         *
         * 1. SweetTooth_InvalidRequestError
         *    - In this case, the data we've sent to ST is invalid, so sending it again won't resolve
         *      this. An example of this is a poorly formatted email address (eg. 'fakeemail') which 
         *      Magento allows for, but the ST Platform does not. When this customer's data is changed
         *      the in_sync flag will be 0, and a sync will be retried, hopefully with a valid request.
         *      
         * 2. ST_Core_Exception_InvalidCustomerBinding
         *    - If the customer is invalid, and we're trying to sync an order by that customer, then
         *      the remote_id for that customer will not exist until the customer info becomes valid and is
         *      created remotely. Until this happens, all bindings (activities) for this customer will
         *      remain in error state.
         */
        if ($exceptionType == "SweetTooth_InvalidRequestError" || $exceptionType == "ST_Core_Exception_InvalidCustomerBinding") {
            $this->setInSync(true);
        }
    }

    /**
     * Sets the binding in a success state and sets the remote
     * object as an instance variable if one is passed in.
     * 
     * @param array $remoteObject the object returned from ST Platform
     */
    public function addSuccess($remoteObject = null)
    {
        if ($remoteObject) {
            $this->setRemoteObject($remoteObject)
                ->setRemoteId($remoteObject->id);
        }

        $this->setInSync(true)
            ->setSyncedAt(new \DateTime())
            ->setInErrorState(false)
            ->setErrorType(null)
            ->setErrorMessage(null);
    }

    public function setRemoteObject($remoteObject)
    {
        $this->_remoteObject = $remoteObject;
        return $this;
    }

    public function getRemoteObject()
    {
        return $this->_remoteObject;
    }
}
