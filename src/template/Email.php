<?php

namespace LinCmsAsync\template;

use PHPMailer\PHPMailer\PHPMailer, PHPMailer\PHPMailer\Exception;
use think\facade\Config;


class Email implements Template
{
    /**
     * @param  array $arguments 调用时的参数
     * 在run()方法中执行你的业务逻辑
     */
    public function run($arguments)
    {
        # 解析参数
        list($to, $title, $content) = $arguments;
        # 调用具体的发送方法或者直接写业务逻辑
        $this->sendEmail($to, $title, $content);
    }

    private function sendEmail($to, $title, $content)
    {
        if ((!is_array($to) && !is_string($to)) || empty($to))
            return '';
        $config = Config::pull('email');
        try {
            $mail = new PHPMailer($config['debug']);
            $mail->isSMTP();
            $mail->CharSet = $config['char_set'];
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPSecure = $config['smtp_secure'];
            $mail->Port = $config['port'];
            $mail->setFrom($config['username'], $config['send_name']);
            if (is_string($to)) {
                $mail->addAddress($to, '');
            } else {
                foreach ($to as $v) {
                    $mail->addAddress($v, '');
                }
            }
            $mail->addReplyTo($config['username'], 'info');
            $mail->isHTML($config['is_html']);
            $mail->Subject = $title;
            $mail->Body = $content;
            $mail->AltBody = $config['alt_body'];
            // $mail->WordWrap = 50;                                 //多少字换行
//            $mail->addAttachment('/data/wwwroot/PHP7.3.pdf');         // Add attachments 添加附件
//            $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name 发送附件并重命名
            $mail->send();
            return true;
        } catch (Exception $e) {
            //或日志记录
            throw new PushException([
                'code' => '500',
                'msg' => '邮件发送失败：', $mail->ErrorInfo
            ]);

        }
    }
}