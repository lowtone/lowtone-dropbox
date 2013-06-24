$ = @jQuery

dropbox = @Dropbox

chooser_options = @lowtone_dropbox_chooser

$.fn.dropbox_chooser = (method) ->
	defaults = 
		linkType: 'direct'
		multiselect: false

	chooser_callback = (files, options) ->
		data = 
			action: "lowtone_dropbox_chooser_#{options.chooser_id}"
			chooser_id: options.chooser_id
			files: files

		success = (response) ->
			return if !response.meta

			switch response.meta.code
				when 200
					return if !response.data

					console.dir response.data

		$.getJSON chooser_options.ajaxurl, data, success

	methods = 
		init: (options) ->
			$element = $ this

			options = $.extend null, ($element.data 'chooser' || {}), options

			$element.click ->
				methods.open options

		open: (options) ->
			options.success = (files) ->
				chooser_callback files, options

			options = $.extend null, defaults, options

			dropbox.choose options

	if methods[method]
		methods[method].apply this, Array::slice.call(arguments, 1)
	else if typeof method is "object" or not method
		methods.init.apply this, arguments
	else
		$.error "Method #{method} does not exist on jQuery.picker"

$ ->
	$('.lowtone.dropbox.chooser').dropbox_chooser()