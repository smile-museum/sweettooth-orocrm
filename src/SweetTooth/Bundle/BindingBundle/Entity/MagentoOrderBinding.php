<?php

namespace SweetTooth\Bundle\BindingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MagentoOrderBinding
 *
 * @ORM\Entity
 * @ORM\Table(name="sweettooth_magento_order_binding",
 *  uniqueConstraints={@ORM\UniqueConstraint(name="UNQ_SWEETTOOTH_MAGENTO_ORDER_BINDING_LOCAL_ID", columns={"local_id"})}
 * )
 */
class MagentoOrderBinding extends BindingAbstract
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="local_id", type="integer", length=11)
     */
    private $local_id;

    /**
     * @var string
     *
     * @ORM\Column(name="remote_id", type="string", length=255, nullable=true)
     */
    private $remote_id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="in_sync", type="boolean")
     */
    private $in_sync;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="synced_at", type="datetime", nullable=true)
     */
    private $synced_at;

    /**
     * @var boolean
     *
     * @ORM\Column(name="in_error_state", type="boolean", nullable=true)
     */
    private $in_error_state;

    /**
     * @var string
     *
     * @ORM\Column(name="error_type", type="string", length=255, nullable=true)
     */
    private $error_type;

    /**
     * @var string
     *
     * @ORM\Column(name="error_message", type="text", nullable=true)
     */
    private $error_message;

    /**
     * @var integer
     *
     * @ORM\Column(name="retry_count", type="integer")
     */
    private $retry_count = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deleted_at;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set local_id
     *
     * @param integer $localId
     * @return MagentoOrderBinding
     */
    public function setLocalId($localId)
    {
        $this->local_id = $localId;

        return $this;
    }

    /**
     * Get local_id
     *
     * @return integer 
     */
    public function getLocalId()
    {
        return $this->local_id;
    }

    /**
     * Set remote_id
     *
     * @param string $remoteId
     * @return MagentoOrderBinding
     */
    public function setRemoteId($remoteId)
    {
        $this->remote_id = $remoteId;

        return $this;
    }

    /**
     * Get remote_id
     *
     * @return string 
     */
    public function getRemoteId()
    {
        return $this->remote_id;
    }

    /**
     * Set in_sync
     *
     * @param boolean $inSync
     * @return MagentoOrderBinding
     */
    public function setInSync($inSync)
    {
        $this->in_sync = $inSync;

        return $this;
    }

    /**
     * Get in_sync
     *
     * @return boolean 
     */
    public function getInSync()
    {
        return $this->in_sync;
    }

    /**
     * Set synced_at
     *
     * @param \DateTime $syncedAt
     * @return MagentoOrderBinding
     */
    public function setSyncedAt($syncedAt)
    {
        $this->synced_at = $syncedAt;

        return $this;
    }

    /**
     * Get synced_at
     *
     * @return \DateTime 
     */
    public function getSyncedAt()
    {
        return $this->synced_at;
    }

    /**
     * Set in_error_state
     *
     * @param boolean $inErrorState
     * @return MagentoOrderBinding
     */
    public function setInErrorState($inErrorState)
    {
        $this->in_error_state = $inErrorState;

        return $this;
    }

    /**
     * Get in_error_state
     *
     * @return boolean 
     */
    public function getInErrorState()
    {
        return $this->in_error_state;
    }

    /**
     * Set error_type
     *
     * @param string $errorType
     * @return MagentoOrderBinding
     */
    public function setErrorType($errorType)
    {
        $this->error_type = $errorType;

        return $this;
    }

    /**
     * Get error_type
     *
     * @return string 
     */
    public function getErrorType()
    {
        return $this->error_type;
    }

    /**
     * Set error_message
     *
     * @param string $errorMessage
     * @return MagentoOrderBinding
     */
    public function setErrorMessage($errorMessage)
    {
        $this->error_message = $errorMessage;

        return $this;
    }

    /**
     * Get error_message
     *
     * @return string 
     */
    public function getErrorMessage()
    {
        return $this->error_message;
    }

    /**
     * Set retry_count
     *
     * @param integer $retryCount
     * @return MagentoOrderBinding
     */
    public function setRetryCount($retryCount)
    {
        $this->retry_count = $retryCount;

        return $this;
    }

    /**
     * Get retry_count
     *
     * @return integer 
     */
    public function getRetryCount()
    {
        return $this->retry_count;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return MagentoOrderBinding
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return MagentoOrderBinding
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set deleted_at
     *
     * @param \DateTime $deletedAt
     * @return MagentoOrderBinding
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deleted_at = $deletedAt;

        return $this;
    }

    /**
     * Get deleted_at
     *
     * @return \DateTime 
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }
}
