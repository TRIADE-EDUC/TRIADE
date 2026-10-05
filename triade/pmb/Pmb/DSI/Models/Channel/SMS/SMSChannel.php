<?php
// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: SMSChannel.php,v 1.8 2023/07/27 13:01:35 rtigero Exp $

namespace Pmb\DSI\Models\Channel\SMS;

use Pmb\Common\Orm\EmprOrm;
use Pmb\DSI\Helper\LookupHelper;
use Pmb\DSI\Helper\SubscriberHelper;
use Pmb\DSI\Models\Channel\RootChannel;

class SMSChannel extends RootChannel
{
    
	public const CHANNEL_REQUIREMENTS = [
		"subscribers" => [
			"phone" => [
				"input_type" => "tel",
				"input_label" => "subscriber_phone"
			]
		]
	];

	public function send($subscribers, $renderedView, $diffusion = null)
	{
	    global $empr_sms_config;

	    if (empty($empr_sms_config)) {
	        // non configure
	        return false;
	    }

		$sms = \sms_factory::make();
		if (!is_object($sms)) {
		    // Aucune classe php
			return false;
		}

		$result = false;
        foreach ($subscribers as $subscriber) {

			if (isset($subscriber->settings->idEmpr)) {
				$empr = new EmprOrm($subscriber->settings->idEmpr);
				$tel = $empr->empr_tel1 ?? null;
            } else {
				$tel = $subscriber->settings->tel ?? null;
            }

			if (! empty($tel)) {
				$text = LookupHelper::format(SubscriberHelper::format($renderedView, $subscriber, true, $diffusion), $diffusion, true);
				$result = $sms->send_sms($tel, $text);
			}
		}

        if (!$result) {
            return [
                "error" => true,
                "errorMessage" => "diffusion_error_sms",
            ];
        }
        return true;
    }
}

