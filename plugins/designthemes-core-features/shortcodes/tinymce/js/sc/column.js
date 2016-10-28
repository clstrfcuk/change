scnShortcodeMeta = {
	attributes : [ 
		{
			label : "Style",
			id : "type",
			help : "Select which type of pricing table you would like to use.",
			controlType : "select-control",
			selectValues : [ 'type1', 'type2' ],
			defaultValue : 'type1',
			defaultText : 'type1'
		},
		{
			label : "Columns",
			id : "content",
			controlType : "column-control"
		},
		{
			label : "Region",
			id : "region",
			help : "Change this option to inner only if you need column inside another column ",
			controlType : "select-control",
			selectValues : [ "Outer", "Inner" ],
			defaultValue : 'Outer',
			defaultText : 'Outer'
		}
	],

	customMakeShortcode : function(b) {
		var a = b.data;
		var region = b.region;
		var type = b.type;

		type = ' type =" '+type+'"';

		if (!a)
			return "";
		b = a.numColumns;
		var c = a.content;
		a = [ "0", "one", "two", "three", "four", "five",'six' ];
		var x = [ "0", "0", "half", "third", "fourth", "fifth",'sixth' ];
		var f = x[b];
		// f += "col_";
		c = c.split("|");
		var g = "";
		for ( var h in c) {
			var d = jQuery.trim(c[h]);
			if (d.length > 0) {
				var e = a[d.length] + '_' + f;
				if (b == 4 && d.length == 2)
					e = "one_half";
					
				if(region == 'Inner') {
					e += "_inner";
				}

				var z = e;
				if (h == 0)
					e += " first";
				g += "[dt_sc_" + e + type +"]Content for " + d.length + "/" + b
						+ " Column here[/dt_sc_" + z + "] <br/>";
			}
		}
		return g;
	}
};