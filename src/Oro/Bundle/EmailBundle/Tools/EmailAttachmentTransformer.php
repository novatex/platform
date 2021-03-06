<?php

namespace Oro\Bundle\EmailBundle\Tools;

use Gaufrette\Filesystem;

use Knp\Bundle\GaufretteBundle\FilesystemMap;

use Symfony\Component\HttpFoundation\File\UploadedFile;

use Oro\Bundle\AttachmentBundle\Entity\Attachment as AttachmentOro;
use Oro\Bundle\EmailBundle\Entity\EmailAttachment as AttachmentEntity;
use Oro\Bundle\EmailBundle\Entity\EmailAttachmentContent;
use Oro\Bundle\EmailBundle\Form\Model\EmailAttachment as AttachmentModel;
use Oro\Bundle\EmailBundle\Form\Model\Factory;

/**
 * Class EmailAttachmentTransformer
 *
 * @package Oro\Bundle\EmailBundle\Tools
 */
class EmailAttachmentTransformer
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Factory
     */
    protected $factory;

    /**
     * @param FilesystemMap $filesystemMap
     * @param Factory       $factory
     */
    public function __construct(FilesystemMap $filesystemMap, Factory $factory)
    {
        $this->filesystem = $filesystemMap->get('attachments');
        $this->factory    = $factory;
    }

    /**
     * @param AttachmentEntity $attachmentEntity
     *
     * @return AttachmentModel
     */
    public function entityToModel(AttachmentEntity $attachmentEntity)
    {
        $attachmentModel = $this->factory->getEmailAttachment();

        $attachmentModel->setEmailAttachment($attachmentEntity);
        $attachmentModel->setType(AttachmentModel::TYPE_EMAIL_ATTACHMENT);
        $attachmentModel->setId($attachmentEntity->getId());
        $attachmentModel->setFileSize(strlen($attachmentEntity->getContent()->getContent()));
        $attachmentModel->setModified($attachmentEntity->getEmailBody()->getCreated());

        return $attachmentModel;
    }

    /**
     * @param AttachmentOro $attachmentOro
     *
     * @return AttachmentModel
     */
    public function oroToModel(AttachmentOro $attachmentOro)
    {
        $attachmentModel = $this->factory->getEmailAttachment();

        $attachmentModel->setType(AttachmentModel::TYPE_ATTACHMENT);
        $attachmentModel->setId($attachmentOro->getId());
        $attachmentModel->setFileName($attachmentOro->getFile()->getOriginalFilename());
        $attachmentModel->setFileSize($attachmentOro->getFile()->getFileSize());
        $attachmentModel->setModified($attachmentOro->getCreatedAt());

        return $attachmentModel;
    }

    /**
     * @param AttachmentOro $attachmentOro
     *
     * @return AttachmentEntity
     */
    public function oroToEntity(AttachmentOro $attachmentOro)
    {
        $emailAttachmentEntity = new AttachmentEntity();

        $emailAttachmentEntity->setFileName($attachmentOro->getFile()->getFilename());

        $emailAttachmentContent = new EmailAttachmentContent();
        $emailAttachmentContent->setContent(
            base64_encode($this->filesystem->get($attachmentOro->getFile()->getFilename())->getContent())
        );

        $emailAttachmentContent->setContentTransferEncoding('base64');
        $emailAttachmentContent->setEmailAttachment($emailAttachmentEntity);

        $emailAttachmentEntity->setContent($emailAttachmentContent);
        $emailAttachmentEntity->setContentType($attachmentOro->getFile()->getMimeType());
        $emailAttachmentEntity->setFile($attachmentOro->getFile());
        $emailAttachmentEntity->setFileName(
            $attachmentOro->getFile()->getOriginalFilename()
        );

        return $emailAttachmentEntity;
    }

    /**
     * @param UploadedFile $uploadedFile
     *
     * @return AttachmentEntity
     */
    public function entityFromUploadedFile(UploadedFile $uploadedFile)
    {
        $emailAttachment = new AttachmentEntity();

        $attachmentContent = new EmailAttachmentContent();
        $attachmentContent->setContent(
            base64_encode(file_get_contents($uploadedFile->getRealPath()))
        );
        $attachmentContent->setContentTransferEncoding('base64');
        $attachmentContent->setEmailAttachment($emailAttachment);

        $emailAttachment->setContent($attachmentContent);
        $emailAttachment->setContentType($uploadedFile->getMimeType());
        $emailAttachment->setFileName($uploadedFile->getClientOriginalName());

        return $emailAttachment;
    }
}
