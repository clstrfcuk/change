scnShortcodeMeta = {
	attributes : [ {
		label : "Style",
		id : "type",
		help : "Select which type of iconbox you would like to use.",
		controlType : "select-control",
		selectValues : [ 'type1', 'type2', 'type3', 'type4', 'type5', 'type6', 'type7', 'type8', 'type9', 'type10', 'type11', 'type12', 'type13', 'type14' ],
		defaultValue : 'type1',
		defaultText : 'type1'
	}, {
		label : "Columns",
		id : "content",
		controlType : "column-control"
	},
	{
		label : "Custom Icon Image URL",
		id : "custom_icon",
		help : 'You can add custom icon image url here',
		controlType : "text-control"
	},
	{
		label : "Icon Background Color",
		id : "custom_bgcolor",
		help : 'You choose your own color to use as the background for your icon. This option is applicable only for type13',
		controlType : "color-control"
	},

	 ],
	customMakeShortcode : function(b) {
		var a = b.data, type = b.type, ctype = type, custom_icon = b.custom_icon, custom_bgcolor = b.custom_bgcolor;

		type = ' type =" '+type+'"';
		
		var icons = ["bell","cogs","leaf","trophy","flag","home","key"];

		if (!a)
			return "";
		b = a.numColumns;
		var c = a.content;
		a = [ "0", "one", "two", "three", "four", "five", 'six' ];
		var x = [ "0", "0", "half", "third", "fourth", "fifth", 'sixth' ];
		var f = x[b];
		c = c.split("|");
		var g = "";
		for ( var h in c) {
			var d = jQuery.trim(c[h]);
			if (d.length > 0) {
				var e = a[d.length] + '_' + f;
				if (b == 4 && d.length == 2)
					e = "one_half";

				var z = e;
				var selected = "";
				if (h == 0) {
					e += " first";
				}
				
			var current_icon = icons[Math.floor(Math.random() * icons.length)];
	
			if(ctype == 'type13') {
				
				g += "[dt_sc_"
					+ e
					+ "] <br/>"
					+ '[dt_sc_icon_box ' + type + ' fontawesome_icon="'+current_icon +'" stroke_icon="" title="Title Comes Here" link="#" custom_icon="'+custom_icon +'" custom_bgcolor="'+custom_bgcolor +'" /]'
				g += " <br> [/dt_sc_" + z
					+ "]";
				
			} else if(ctype == 'type14') {
				
				g += "[dt_sc_"
					+ e
					+ "] <br/>"
					+ '[dt_sc_icon_box ' + type + ' fontawesome_icon="'+current_icon +'" stroke_icon="" title="Title Comes Here" link="#" custom_icon="'+custom_icon +'" /]'
				g += " <br> [/dt_sc_" + z
					+ "]";
				
				
			} else {
				
				g += "[dt_sc_"
					+ e
					+ "] <br/>"
					+ '[dt_sc_icon_box ' + type + ' fontawesome_icon="'+current_icon +'" stroke_icon="" title="Title Comes Here" link="#" custom_icon="'+custom_icon +'"]<br>'
					+ ' <p> Nunc at pretium est curabitur commodo leac est venenatis egestas sed aliquet auguevelit. </p>';
				g += " [/dt_sc_icon_box]"
					+" <br> [/dt_sc_" + z
					+ "]";
				
			}
			
				
			}
		}

		return g;
	}
};