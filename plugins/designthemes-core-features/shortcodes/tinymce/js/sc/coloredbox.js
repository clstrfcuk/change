scnShortcodeMeta = {
	attributes : [ {
		label : "Style",
		id : "type",
		help : "Select which type of pricing table you would like to use.",
		controlType : "select-control",
		selectValues : [ 'type1', 'type2' ],
		defaultValue : 'type1',
		defaultText : 'type1'
	}, {
		label : "Columns",
		id : "content",
		controlType : "column-control"
	} ],
	customMakeShortcode : function(b) {
		var a = b.data, type = b.type;
		var icons = ["bell","cogs","leaf","trophy","flag","home","key"];
		var c_class = ( type == "type1" ) ? " class='no-space' " : " class='space' ";

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
			var current_icon = icons[Math.floor(Math.random() * icons.length)];
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
				
				
				g += "[dt_sc_"
					+ e
					+ c_class
					+ "] <br/>"
					+ "[dt_sc_icon_box_colored fontawesome_icon='"+current_icon +"' title='Well Trained Professionals' bgcolor='#333334' ]<br>"
					+ ' <p> Nunc at pretium est curabitur commodo leac est venenatis egestas sed aliquet auguevelit. </p>'
					+ ' [dt_sc_button link="#" size="small" target="_blank"]Know More[/dt_sc_button]<br>'
					+ " [/dt_sc_icon_box_colored]"
					+" <br> [/dt_sc_" + z
					+ "] <br/>";
						
			}
		}
		return g;
	}
};