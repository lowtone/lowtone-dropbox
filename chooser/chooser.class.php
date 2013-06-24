<?php
namespace lowtone\dropbox\chooser;
use lowtone\db\records\Record;

/**
 * @author Paul van der Meijs <code@lowtone.nl>
 * @copyright Copyright (c) 2013, Paul van der Meijs
 * @license http://wordpress.lowtone.nl/license/
 * @version 1.0
 * @package wordpress\libs\lowtone\dropbox\chooser
 */
class Chooser extends Record {

	const PROPERTY_ID = "chooser_id",
		PROPERTY_CALLBACK = "callback";

	public function __construct($input = NULL, $flags = 0, $iterator_class = "ArrayIterator") {
		parent::__construct($input, $flags, $iterator_class);

		add_action("wp_ajax_lowtone_dropbox_chooser_" . $this->{self::PROPERTY_ID}, array($this, "doCallback"));
	}

	public function doCallback() {
		if (is_callable($callback = $this->{self::PROPERTY_CALLBACK}))
			call_user_func($callback);
	}

	public function button($options = NULL) {
		if (!\lowtone\dropbox\key())
			return '<!-- Dropbox Chooser requires a valid key -->';

		$options = array_merge(array(
				"text" => __("Select a file", "lowtone_dropbox"),
				"class" => "button button-primary",
			), (array) $options);

		$chooserId = "lowtone_dropbox_chooser_" . $this->{self::PROPERTY_ID};

		\lowtone\dropbox\enqueueScript();

		wp_enqueue_script("lowtone_dropbox_chooser", LIB_URL . "/lowtone-dropbox/chooser/assets/scripts/jquery.dropbox-chooser.js", array("jquery"));
		wp_localize_script("lowtone_dropbox_chooser", "lowtone_dropbox_chooser", array(
				"ajaxurl" => admin_url("admin-ajax.php"),
			));

		$chooserOptions = array_intersect_key((array) $this, array_flip(array(self::PROPERTY_ID)));

		return sprintf(
				'<button id="%s" class="lowtone dropbox chooser %s" data-chooser="%s">%s</button>', 
				esc_attr($chooserId), 
				esc_attr(implode(" ", (array) $options["class"])), 
				esc_attr(json_encode($chooserOptions)),
				esc_html($options["text"])
			);
	}

}