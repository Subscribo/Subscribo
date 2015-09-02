<?php

namespace Subscribo\ApiServerCommon\Traits;

use RuntimeException;
use Subscribo\ModelCore\Models\Message;
use Subscribo\ApiServerCommon\Utils\EmailUtils;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\View\Factory;

/**
 * Trait EmailSendingTrait
 *
 * @package Subscribo\ApiServerCommon
 */
trait EmailSendingTrait
{
    /**
     * @param Mailer $mailer
     * @param Message $message
     * @param string|null $templatePath
     * @param array $viewData
     * @return bool|null
     */
    protected static function sendEmail(
        Mailer $mailer,
        Message $message,
        $templatePath = null,
        array $viewData = []
    ) {
        if (Message::STATUS_SENT === $message->status) {

            return null;
        }
        $message->addEmailToFromAccount();
        $message->synchroniseSubject();
        $emailData = $message->messageData ?: [];
        $message->status = Message::STATUS_PREPARED;
        if (isset($message->content)) {
            $message->save();
            $mailer->raw($message->content, function ($m) use ($emailData) {
                EmailUtils::enhanceEmailMessage($m, $emailData);
            });
        } else {
            $factory = static::obtainViewFactory();
            $views = static::assembleEmailViews($templatePath, $factory);
            $contentTemplate = empty($views['text']) ? reset($views) : $views['text'];
            $message->content = $factory->make($contentTemplate, $viewData)->render();
            $message->save();
            $mailer->send($views, $viewData, function ($m) use ($emailData) {
                EmailUtils::enhanceEmailMessage($m, $emailData);
            });
        }
        if ($mailer->failures()) {
            $message->status = Message::STATUS_FAILED;
        } else {
            $message->status = Message::STATUS_SENT;
        }
        $message->save();

        return (Message::STATUS_SENT === $message->status);
    }

    /**
     * @param $templatePath
     * @param Factory $viewFactory
     * @return array
     * @throws \RuntimeException
     */
    protected static function assembleEmailViews($templatePath, Factory $viewFactory)
    {
        if (empty($templatePath)) {
            throw new RuntimeException('Template path empty');
        }
        $views = [];
        if ($viewFactory->exists($templatePath.'.text')) {
            $views['text'] = $templatePath.'.text';
        }
        if ($viewFactory->exists($templatePath.'.html')) {
            $views['html'] = $templatePath.'.html';
        }
        if ($viewFactory->exists($templatePath.'.raw')) {
            $views['raw'] = $templatePath.'.raw';
        }
        if (empty($views)) {
            throw new RuntimeException('No email templates found');
        }

        return $views;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory
     */
    protected static function obtainViewFactory()
    {
        return view();
    }
}
