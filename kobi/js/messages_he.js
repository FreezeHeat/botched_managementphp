/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: HE (Hebrew; עברית)
 */
$.extend( $.validator.messages, {
	required: "השדה הזה הינו שדה חובה",
	remote: "נא לתקן שדה זה",
	email: "נא למלא כתובת דוא\"ל חוקית",
	url: "נא למלא כתובת אינטרנט חוקית",
	date: "נא למלא תאריך חוקי",
	dateISO: "נא למלא תאריך חוקי (ISO)",
	number: "נא למלא מספר",
	digits: "נא למלא רק מספרים",
	creditcard: "נא למלא מספר כרטיס אשראי חוקי",
	equalTo: "נא למלא את אותו ערך שוב",
	extension: "נא למלא ערך עם סיומת חוקית",
	maxlength: $.validator.format( ".נא לא למלא יותר מ- {0} תווים" ),
	minlength: $.validator.format( "נא למלא לפחות {0} תווים" ),
	rangelength: $.validator.format( "נא למלא ערך בין {0} ל- {1} תווים" ),
	range: $.validator.format( "נא למלא ערך בין {0} ל- {1}" ),
	max: $.validator.format( "נא למלא ערך קטן או שווה ל- {0}" ),
	min: $.validator.format( "נא למלא ערך גדול או שווה ל- {0}" )
} );

/*
* Method to handle IL phone numbers
*/
$.validator.addMethod(
        "ilphone",
        function(value, element) {
            var re = new RegExp("^0[0-9]{8,9}$");
            return this.optional(element) || re.test(value);
        },
        "מספר  - צריך לפחות 9 ספרות ולא יותר מ 10"
);

/* 
*	File select helper method (max files) 
*/

$.validator.addMethod("maxFilesToSelect", function(value, element, params) {
	return this.optional(element) || (element.files.length >= 0 && element.files.length <= params[0]);
},  jQuery.validator.format('יש לבחור עד {0} תמונות'));

/* 
*	File select helper method (max file size) 
*/

$.validator.addMethod("maxFileSize", function(value, element) {
	var valid = true;
	for(let i = 0; i < element.files.length; i++){
		
		if(element.files[i].size > 2097152){
			valid = false;
			break;
		}
	}
	return this.optional(element) || valid;
},  'תמונות יתקבלו עד 2 מגה');

/*
*	Validation of text input
*/
jQuery.validator.addMethod("lettersonly", function(value, element) 
{
	return this.optional(element) || /^[א-ת]+$/i.test(value);
}, "הכנס רק אותיות בבקשה");

/*
*	Validation of text input
*/
jQuery.validator.addMethod("lettersonly_spaces", function(value, element) 
{
	return this.optional(element) || /^[א-ת ]+$/i.test(value);
}, "מותר להקליד רק רווחים ואותיות");

/*
*	Validation of text input
*/
jQuery.validator.addMethod("lettersonly_spaces_punctuation", function(value, element) 
{
	return this.optional(element) || /^[א-תa-zA-Z,.'-()! ]+$/i.test(value);
}, "אין להכניס תווים, חוץ מאותיות רווחים וסימני פיסוק");

/*
*	Validation of text input
*/
jQuery.validator.addMethod("lettersonly_spaces_numbers", function(value, element) 
{
	return this.optional(element) || /^[א-ת0-9 ]+$/i.test(value);
}, "מותר להקליד רווחים אותיות ומספרים");

/*
*	Validation of text input
*/
jQuery.validator.addMethod("digitsonly", function(value, element) 
{
	return this.optional(element) || /^[0-9]+$/i.test(value);
}, "מותר להקליד ספרות בלבד");

/*
*	So the plugin won't ignore display: none fields
*/
$.validator.setDefaults({
        ignore: []
    });