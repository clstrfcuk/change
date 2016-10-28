scnShortcodeMeta = {
	attributes : [ {
		label : "Columns",
		id : "content",
		controlType : "column-control"
	},
	{
		label : "Title",
		id : "title",
		help : 'Type out the title to use with your counter box',
		controlType : "text-control"
	},
	{
		label : "Number",
		id : "number",
		help : 'Type out the number to count up',
		controlType : "text-control"
	},
	
	],
	customMakeShortcode : function(b) {
		var a = b.data, title = b.title, number = b.number;
		
		if(title != '') title = ' title="'+title+'"'; else title = '';
		if(number != '') number = ' number="'+number+'"'; else number = '';

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
					selected = "selected";
				}
				var chcnt = eval(h)+1;

				g += "[dt_sc_"
						+ e
						+ "] "
						+ "<br>[dt_sc_counter " + title + " " + number + " /]"
						+ " [/dt_sc_" + z
						+ "] <br/>";
			}
		}

		return "<br>" + g + "<br>";
	}
};