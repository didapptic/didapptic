<?php
declare(strict_types=1);

namespace Didapptic\Object\Notification\Applicant\Mail;

use doganoo\INotify\Handler\Applicant\Mail\IConfig;

class Config implements IConfig {

    /** @var string */
    private $SMTPHost;
    /** @var string */
    private $SMTPPort;
    /** @var string */
    private $SMTPProtocol;

    /**
     * @return string
     */
    public function getSMTPHost(): string {
        return $this->SMTPHost;
    }

    /**
     * @param string $SMTPHost
     */
    public function setSMTPHost(string $SMTPHost): void {
        $this->SMTPHost = $SMTPHost;
    }

    /**
     * @return string
     */
    public function getSMTPPort(): string {
        return $this->SMTPPort;
    }

    /**
     * @param string $SMTPPort
     */
    public function setSMTPPort(string $SMTPPort): void {
        $this->SMTPPort = $SMTPPort;
    }

    /**
     * @return string
     */
    public function getSMTPProtocol(): string {
        return $this->SMTPProtocol;
    }

    /**
     * @param string $SMTPProtocol
     */
    public function setSMTPProtocol(string $SMTPProtocol): void {
        $this->SMTPProtocol = $SMTPProtocol;
    }


}
