ff.ffField.datechooser = (function () {
	var that = { /* publics*/
		__ff : true, /* used to recognize ff'objects*/
		"fill" : function (control_id, control_day_id, control_month_id, control_year_id, sel_day, sel_month, sel_year) {
			var control_day = document.getElementById(control_day_id);
			var control_month = document.getElementById(control_month_id);
			var control_year = document.getElementById(control_year_id);

			if(control_day) {
				for (var i = 0; i <= 31; i++)
				{
					var option = document.createElement('option');
					if(i == 0) {
						option.setAttribute('value', '0');
						option.appendChild(document.createTextNode("-"));
					} else {
						option.setAttribute('value', i);
						if (i < 10) {
							option.setAttribute('value',"0" + i);
							option.appendChild(document.createTextNode("0" + i));
						} else {
							option.setAttribute('value', i);
							option.appendChild(document.createTextNode(i));
						}
					}
					control_day.appendChild(option);
					if(i == sel_day)
						control_day.selectedIndex = i;
				}
			}
			if(control_month) {
				for (var i = 0; i <= 12; i++)
				{
					var option = document.createElement('option');
					if(i == 0) {
						option.setAttribute('value', '0');
						option.appendChild(document.createTextNode("-"));
					} else {
						if (i < 10) {
							option.setAttribute('value',"0" + i);
							option.appendChild(document.createTextNode("0" + i));
						} else {
							option.setAttribute('value', i);
							option.appendChild(document.createTextNode(i));
						}
					}
					control_month.appendChild(option);
					if(i == sel_month)
						control_month.selectedIndex = i;
				}
			}
			if(control_year) {
				for (var i = 1919; i <= 2020; i++) 
				{
					var option = document.createElement('option');
					if(i == 1919) {
						option.setAttribute('value', '0');
						option.appendChild(document.createTextNode("-"));
					} else {
						option.setAttribute('value', i);
						option.appendChild(document.createTextNode(i));
					}
					control_year.appendChild(option);
					if(i == sel_year)
						control_year.selectedIndex = i - 1919;
				}
			}
		}, 
		"change" : function (control_id, control_day_id, control_month_id, control_year_id) {
			var control			= document.getElementById(control_id);
			var control_day		= document.getElementById(control_day_id);
			var control_month	= document.getElementById(control_month_id);
			var control_year	= document.getElementById(control_year_id);

			if(control) {
				control.value = control_day.value + "/" + control_month.value + "/" +  control_year.value; 
			}
		}
	};
	
	return that;

})();