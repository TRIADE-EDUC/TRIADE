<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: MailChannel.php,v 1.26.2.3.2.1 2025/04/01 07:32:22 jparis Exp $
namespace Pmb\DSI\Models\Channel\Mail;

use Pmb\DSI\Helper\LookupHelper;
use Pmb\DSI\Models\Channel\RootChannel;
use Pmb\DSI\Models\SendQueue;
use Pmb\DSI\Models\SubscriberList\RootSubscriberList;
use Pmb\Common\Helper\HTML;
use Pmb\DSI\Models\Stats;
use Pmb\Common\Helper\GlobalContext;
use Pmb\DSI\Helper\{DSIDocument, SubscriberHelper};
use Pmb\DSI\Models\Item\RootItem;
use Pmb\DSI\Models\View\RootView;

class MailChannel extends RootChannel
{

    public const STAT_OPENING = 1;

    public const STAT_CLICK = 2;

    public const CHANNEL_REQUIREMENTS = [
        "subscribers" => [
            "email" => [
                "input_type" => "email",
                "input_label" => "subscriber_email"
            ]
        ]
    ];

    public function __construct(int $id = 0)
    {
        $this->id = $id;
        $this->read();
    }

    public static function getMailList()
    {
        global $msg;

        $list = [
            0 => $msg["dsi_channel_default_mail_selected"]
        ];

        $query = "SELECT id_mail_configuration, name_mail_configuration FROM mails_configuration WHERE mail_configuration_type='address'";
        $result = pmb_mysql_query($query);

        if (!pmb_mysql_num_rows($result)) {
            $list_mails_configuration_domains_ui = \list_mails_configuration_domains_ui::get_instance();
            $list_mails_configuration_domains_ui->initialization();

            $result = pmb_mysql_query($query);
        }

        if (pmb_mysql_num_rows($result)) {
            while ($row = pmb_mysql_fetch_assoc($result)) {
                $mailConfiguration = new \mail_configuration($row['name_mail_configuration']);
                $uses = $mailConfiguration->get_uses();
                if(!empty($uses['users']) || !empty($uses['locations']) || !empty($uses['parameters'])) {
                    $list[$row['id_mail_configuration']] = $row['name_mail_configuration'];
                }
            }
        }

        return $list;
    }

    /**
     *
     * @param array $subscriberList
     * @param string $renderedView
     * @param $diffusion
     *
     * @see \Pmb\DSI\Models\Channel\RootChannel::send()
     */
    public function send($subscriberList, $renderedView, $diffusion = null)
    {
        global $dsi_send_automatically, $pmb_mail_delay;

        $mailDsi = new \mail_dsi();

        if(!empty($this->settings->mail_selected)) {
            $mailList = MailChannel::getMailList();
            if(isset($mailList[$this->settings->mail_selected])) {
                $mail = $mailList[$this->settings->mail_selected];

                $mailDsi->set_setting_value("mail", $mail);
    
                $source = \mail_configuration::get_source_from_mail($mail);
                $mailDsi->set_setting_value("sender", $source['type']);
                $mailDsi->set_mail_from_id($source['id']);
            }
        }

        if (isset($diffusion)) {
            $mailDsi->set_associated_campaign(true);

            if (empty($dsi_send_automatically)) {
                $settings = SendQueue::getSettings($diffusion->currentHistory->idDiffusionHistory);
                if (!empty($settings) && isset($settings->numCampaign)) {
                    $mailDsi->set_associated_num_campaign($settings->numCampaign);
                }
            }

            $campaign = $mailDsi->get_campaign();

            if (empty($dsi_send_automatically) && empty($settings)) {
                SendQueue::initSettings($diffusion->currentHistory->idDiffusionHistory, ["numCampaign" => $campaign->get_id()]);
            }

            $stats = new Stats(static::IDS_TYPE[static::class]);
            $stats->settings->campaign_num = $campaign->get_id();
            $stats->setReport(Stats::REPORT_LINK, "./edit.php?categ=opac&sub=campaigns&action=view&id={$campaign->get_id()}");
            $this->settings->stats = $stats;
        }

        $error = empty($subscriberList) ? true : false;
        foreach ($subscriberList as $subscriber) {
            $object = $this->settings->mail_object;

            if (isset($diffusion)) {
                $object = LookupHelper::replacePattern($object, $diffusion);
                $object = SubscriberHelper::replacePattern($object, $subscriber, $diffusion);
            }

            $mailDsi->set_mail_object($object);

            if (isset($subscriber->settings->idEmpr)) {
                $mailDsi->set_mail_to_id($subscriber->settings->idEmpr);
            } else {
                $mailDsi->set_mail_to_mail($subscriber->settings->email)->set_mail_to_name($subscriber->getName());
            }

            $html = LookupHelper::format(SubscriberHelper::format($renderedView, $subscriber, false, $diffusion), $diffusion);

            if (isset($this->settings->noHtmlFormat) && $this->settings->noHtmlFormat == 1) {
                $content = $html;
            } else {
                $content = HTML::formatRender($html, $this->title);
            }
            $mailDsi->set_mail_content($content);

            if ($this->settings->mail_choice == "mail_attachments") {
                $attachments = [];
                foreach ($diffusion->settings->attachments as $attachment) {
                    $view = RootView::getInstance($attachment->view);
                    $item = RootItem::getInstance($attachment->item);

                    $attachments[] = $view->render($item, $diffusion->id, 0, "attachments");
                }
                $mailDsi->set_mail_attachments($attachments);
            }

            if ($pmb_mail_delay) {
                sleep(intval(round(intval($pmb_mail_delay) / 1000)));
            }

            $sent = $mailDsi->send_mail();
            if (! $sent) {
                $error = true;
            }
        }
        if ($error) {
            return [
                "error" => true,
                "errorMessage" => "diffusion_error_mail"
            ];
        }
        return true;
    }

    public static function fetchStats(Stats $stats)
    {
        $campaignStats = new \campaign_stats($stats->settings->campaign_num ?? 0);

        return [
            static::STAT_OPENING => sprintf(GlobalContext::msg('dsi_opening_rate'), $campaignStats->get_opening_rate()),
            static::STAT_CLICK => sprintf(GlobalContext::msg('dsi_clicks_rate'), $campaignStats->get_clicks_rate())
        ];
    }
}
