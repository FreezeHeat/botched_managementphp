// SERVER DATA
var cfg = $("input[type='hidden']:first").attr('value');
cfg = cfg.split(",");

// Client data properties
const CLIENT = {
	 CID : 'client_id',
	 FNAME : 'firstname',
	 LNAME : 'lastname',
	 CITY : 'city',
	 ADDRESS : 'address',
	 EMAIL : 'email',
	 PHONE_NUM : 'phone_number',
	 JOB_TYPE : 'job_type',
	 DESCRIPTION : 'description',
	 STATUS : 'status',
	 REQUEST_DATE : 'request_date',
	 IMAGES : 'images_path'
};

// Jobs data properties
const JOBS = {
		ID : 'job_id',
		DESCRIPTION : 'description'
};

// Status of requests
const STATUS_MSG = {
	PRE : 'לא טופל',
	APPROVED : 'מאושר',
	CANCELED : 'מסורב'
};

// For button functionality (delete, add etc..)
const TYPE = {JOB : 'jobs', REQUEST : 'requests'};
const METHOD = {ADD : 'add', EDIT : 'edit', REMOVE : 'remove'};

// Button names
// const BTN = {
	// JOB_ADD: "addJob",
	// JOB_EDIT: "editJob",
	// JOB_REMOVE: "removeJob",
	// REQUEST_ADD: "addRequest",
	// REQUEST_EDIT: "editRequest",
	// REQUEST_REMOVE: "removeRequest"
// };

// hold requests and jobs from the server
var requests;
var jobs;

$(document).ready(function(){
	
	// call for timedEvent in 5 minute intervals
	setInterval(function(){
		timedEvent();
	}, 300000); // 5 minutes

	// Requests are the first page to be shown
	getRequests();

	// clicking different sections should load different values into the tab pane

	// requests
	$("a[href='#request_table']").click(function(){
		getRequests();

		// uncheck when accessing another tab
		$("table tr input[type='checkbox']").each(function(index, element){
			this.checked = false;
		});
	});

	// jobs
	$("a[href='#service_list']").click(function(){
		getJobs();

		// uncheck when accessing another tab
		$("table tr input[type='checkbox']").each(function(index, element){
			this.checked = false;
		});
	});

	// Check all checkbox
	$("table tr th input[type='checkbox']").each(function(index, element){
		$(element).click(function(){
			let element = $(this).closest("table").find("input[type='checkbox']");
			if(this.checked){
				element.prop("checked", true);
				element.change();
			}else{
				element.prop("checked", false);
				element.change();
			}
		});
	});

	// Indicate selected items
	$("table").on("change", "tr td input[type='checkbox']", function(event){
		$(this).each(function(index, element){
			if(element.checked){
				$(element).closest("tr").addClass("success");
			}else{
				$(element).closest("tr").removeClass("success");
			}
		});
	});
	
	// Edit text inside td - jobs
	$("#service_list table").on("dblclick", "tr:not(:first) td:not(:first)", function(event){
		let element = $(this);
		$(element).attr("contenteditable", "true").css("cursor", "cell");
		element.focus();
		
		// on mouse leave from the table, return to not editable
		$(element).closest("table").one("mouseleave", function(){
			$(element).attr("contenteditable", "false").css("cursor", "text");
			let text = $(element).html();
			var regex = /<br\s*[\/]?>/gi;
			$(element).html(text.replace(regex, " "));
		});
	});
	
	// Edit text inside td - requests
	$("#request_table table").on("dblclick", "tr:not(:first) td:not( :first, :nth-child(9), :nth-child(10), :nth-child(11) )", function(event){
		let element = $(this);
		$(element).attr("contenteditable", "true").css("cursor", "cell");
		element.focus();
		
		// on mouse leave from the table, return to not editable
		$(element).closest("table").one("mouseleave", function(){
			$(element).attr("contenteditable", "false").css("cursor", "text");
			let text = $(element).html();
			var regex = /<br\s*[\/]?>/gi;
			$(element).html(text.replace(regex, " "));
		});
	});

	// Edit buttons functionality
	$(".tab-content button[name^='edit']").click(function(){
		let json = {type: "" , method: METHOD.EDIT, data: []};
		getAllCheckedAndTypeForEdit(json);
		sendJson(json);
	});
	
	// Delete modal confirm button functionality (delete)
	$('#deleteModal .modal-footer .btn-primary').on('click', function(event) {
	  var $button = $(event.target); // The clicked button
	
	  $(this).closest('.modal').one('hidden.bs.modal', function() {
		  let json = {type: "" , method: METHOD.REMOVE, data: []};
		  getAllCheckedAndType(json);
		  prepareJsonData(json);
		  sendJson(json);
	  });
	});
	
	// Add modal UX focus on input
	$('#addModal').on('shown.bs.modal', function (e) {
		$(this).find(".modal-body input").focus();
	});
	
	// Add modal confirm button functionality
	$('#addModal .modal-footer .btn-primary').on('click', function(event) {
	  $(this).closest('.modal').one('hidden.bs.modal', function() {
		  let json = {type: TYPE.JOB , method: METHOD.ADD, data: []};
		  json.data.push( {'description' : $(this).find(".modal-body input").val()} );
		  
		  // empty for UX
		  $(this).find(".modal-body input").val("");
		  sendJson(json);
	  });
	});
	
	// prevent Add modal form (so it will be used only for validation not for submission) from submit
	$('#addForm').submit(function (event) {
		event.preventDefault();
		let modal = $(this).closest('.modal');
		modal.one('hidden.bs.modal', function() {
		  let json = {type: TYPE.JOB , method: METHOD.ADD, data: []};
		  json.data.push( {'description' : $(this).find(".modal-body input").val()} );
		  
		  // empty for UX
		  $(this).find(".modal-body input").val("");
		  sendJson(json);
		});
		modal.modal('hide');
	});

	// Show images button functionality
	$("#request_table table").on("click", "input[value='הצג תמונות']", function(event){
		let client_cid = $(this).closest("tr").find("input[type='hidden']").attr("value"); // client id based on client clicked
		let carousel_images = ""; // carousel script to append
		let num_images = 0; // number of images for each client
	
		// find the corresponding CLIENT.CID and check if there are image paths
		for(let i = 0, url = ""; i < requests.length; i++){
			if(requests[i][CLIENT.CID] == client_cid){
				if(Array.isArray(requests[i][CLIENT.IMAGES])){
					num_images = requests[i][CLIENT.IMAGES].length;

					// first element must have class 'active' to work with the carousel (Bootstrap)
					url = location.protocol + "//" + location.host + requests[i][CLIENT.IMAGES][0];
					carousel_images += "<div class='item active'>" +
													"	<a class='thumbnail' href='" + url + "'>" +
													"		<img src='" + url + "'>" +
													"	</a>" +
													"</div>"

					for(let k = 1; k < num_images; k++){
						// correct URL for images, then append to carousel
						url = location.protocol + "//" + location.host + requests[i][CLIENT.IMAGES][k];
						carousel_images += "<div class='item'>" +
										"	<a class='thumbnail' href='" + url + "'>" +
										"		<img src='" + url + "'>" +
										"	</a>" +
										"</div>"	
					}
				}
				break;
			}
		}

		//empty carousel indicators and add based on number of images (unless it's just one image)
		$("#imgModal .modal-body .carousel-indicators").empty();
		if(num_images > 1){
			let carousel_indicators = 
				"<li data-target='#img_carousel' data-slide-to='0' class='active'></li>";
			for(let i = 1; i < num_images ; i++){
				carousel_indicators += "	<li data-target='#img_carousel' data-slide-to='" + i + "'></li>";
			}
			$("#imgModal .modal-body .carousel-indicators").append(carousel_indicators);
		}

		// empty carousel pictures and append the new ones based on client clicked
		$("#imgModal .modal-body .carousel-inner").empty();
		$("#imgModal .modal-body .carousel-inner").append(carousel_images);
		$("#imgModal").modal();
	});
	
	// Select list status indicators
	$("#request_table table").on("change", "select", function(event){
		let status = {css: "", value: ""};

		// color-coded request status
		switch(this.value){
			case STATUS_MSG.PRE :
				status.css = 'background-color';
				status.value = 'rgb(22, 164, 0)';
				break;
			case STATUS_MSG.APPROVED :
				status.css = 'background-color';
				status.value = 'rgb(0, 78, 173)';
				break;
			case STATUS_MSG.CANCELED :
				status.css = 'background-color';
				status.value = 'rgb(203, 2, 2)';
				break;
		}

		// color-coded status
		$(this).css(status.css, status.value).css('color', 'white');
	});
});

// Get all requests from the server and populate the table
function getRequests(){

	// on load, load all requests (it's shown first)
	$.get(window.location.href.split("#")[0].split("?")[0] + "/getRequests", function(data){
		$("#request_table table tr:not(.info)").remove();
		requests = JSON.parse(data);
		for(let i = 0; i < requests.length; i++){

			// auxiliary data
			let images = "";
			let message = "";

			// color-coded request status
			switch(requests[i][CLIENT.STATUS].toString()){
				case '0':
					message = STATUS_MSG.PRE;
					break;
				case '1':
					message = STATUS_MSG.APPROVED;
					break;
				case '2':
					message = STATUS_MSG.CANCELED;
					break;
			}

			// if there are images, add a button to view them
			if( Array.isArray(requests[i][CLIENT.IMAGES]) ){
				images += "<input class='btn btn-default btn_responsive' type='button' value='הצג תמונות'>";
			}
 
			$("#request_table table").append("<tr><td><input type=\"checkbox\"></td>");
			$("#request_table tr:last").append(
				"<td>" + requests[i][CLIENT.FNAME] + " " + requests[i][CLIENT.LNAME] + "</td>" +
				"<td>" + requests[i][CLIENT.CITY] + "</td>" +
				"<td>" + requests[i][CLIENT.ADDRESS] + "</td>" +
				"<td>" + requests[i][CLIENT.EMAIL] + "</td>" +
				"<td>" + requests[i][CLIENT.PHONE_NUM] + "</td>" +
				"<td>" + requests[i][CLIENT.JOB_TYPE] + "</td>" +
				"<td>" + requests[i][CLIENT.DESCRIPTION] + "</td>" +
				"<td class='text-center'>" + images + "</td>" +
				"<td><select class='form-control'>"+
				"<option value='" + STATUS_MSG.PRE + "'>" + STATUS_MSG.PRE + "</option>" + 
				"<option value='" + STATUS_MSG.APPROVED + "'>" + STATUS_MSG.APPROVED + "</option>" +
				"<option value='" + STATUS_MSG.CANCELED + "'>" + STATUS_MSG.CANCELED + "</option>" +
				"</select></td>" +
				"<td class='text-center'>" + requests[i][CLIENT.REQUEST_DATE] + "</td>" +
				"<input type='hidden' value='" + requests[i][CLIENT.CID] + "'>"
			);

			// set the option from the database into the select element and update it (trigger change)
			$("#request_table table tr:last select").prop("value", message).change();
			$("#request_table table").append("</tr>");
		}
	});
}

function getJobs(){
	$.get(window.location.href.split("#")[0].split("?")[0] + "/getJobs", function(data){
		$("#service_list table tr:not(.info)").remove();
		jobs = JSON.parse(data);
		for(let i = 0; i < jobs.length; i++){
			$("#service_list table").append("<tr><td><input type=\"checkbox\"></td>");
			$("#service_list table tr:last").append("<td>" + jobs[i][JOBS.DESCRIPTION] + "</td>");
			$("#service_list table tr:last").append("<input type='hidden' value='" + jobs[i][JOBS.ID] + "'>");
		}
		$("#service_list table").append("</tr>");
	});
}

// get all checked items and discern the json data type
function getAllCheckedAndType(json){
	let id = "";
	
	// compile all checked table items
	$("table tr td input[type='checkbox']").each(function(index, element){
		if(element.checked){
			(json.data).push($(element).closest("tr").find("input[type='hidden']").attr("value"));
			id = $(element).closest(".tab-pane").attr("id");
		}
	});
	
	// based on the ID, the type can be found
	json.type = (id === "request_table") ? TYPE.REQUEST : TYPE.JOB;
}

// get all checked items and their edited values, and discern the json data type
function getAllCheckedAndTypeForEdit(json){
	let id = "";
	
	// compile all checked table items
	$("table tr td input[type='checkbox']").each(function(index, element){
		if(element.checked){
			id = $(element).closest(".tab-pane").attr("id");
			
			// based on type, get the new edited values
			let row = $(element).closest("tr");
			if(id === "service_list"){
				(json.data).push(
					{ 
						'job_id' : row.find("input[type='hidden']").attr("value"),
						'description': row.find("td:not(:first)").text() 
					}
				);
			}else{
				let values = []; // edited values from the table
				let td_array = row.find("td:not(:first)");
				values.push(td_array[0].innerText.split(" ")[0]); // name
				values.push (td_array[0].innerText.split(" ")[1]);  
				values.push(td_array[1].innerText); // city
				values.push(td_array[2].innerText); // address 
				values.push(td_array[3].innerText); // email
				values.push(td_array[4].innerText); // phone
				values.push(td_array[5].innerText); // service
				values.push(td_array[6].innerText); // description
				values.push($(td_array[8]).find("select")[0].selectedIndex); // status
				(json.data).push(
					{
						'client_id' : row.find("input[type='hidden']").attr("value"),
						"values" : values
					}
				);
			}
		}
	});
	
	// based on the ID, the type can be found
	json.type = (id === "request_table") ? TYPE.REQUEST : TYPE.JOB;
}

// Prepare json data (get ID's)
function prepareJsonData(json){

	// check with the requests or job
	if(json.type === TYPE.JOB){
		for(let i = 0; i < json.data.length; i++){
			for(let k = 0; k < jobs.length; k++){
				if(json.data[i] === jobs[k][JOBS.ID]){
					json.data[i] = jobs.splice(k, 1)[0]; // remove the match (faster iteration) and keep it in cid_array
					break;
				}
			}
		}
	}else{
		for(let i = 0; i < json.data.length; i++){
			for(let k = 0; k < requests.length; k++){
				if(json.data[i] === requests[k][CLIENT.CID]){
					json.data[i] = requests.splice(k, 1)[0]; // remove the match (faster iteration) and keep it in cid_array
					break;
				}
			}
		}
	}
}

// build a JSON object to send to the server based on button name
// function buildJson(name){
	// var json = {type: "", method: "", data: []};

	// switch(name){
		// case BTN.JOB_ADD:
				// json.method = METHOD.ADD;
				// return false;
			// break;
		// case BTN.JOB_EDIT:
				// json.method = METHOD.EDIT;
				// return false; // TODO: change
			// break;
		// case BTN.JOB_REMOVE:
				// return false;
			// break;
		// case BTN.REQUEST_EDIT:
				// json.method = METHOD.EDIT;
				// return false; // TODO: change
			// break;
		// case BTN.REQUEST_REMOVE:
				// return false;
			// break;
	// }
	// return json;
// }

// send the JSON object to the server
function sendJson(json){
	if(json.type != undefined && json.data != undefined && json.data.length > 0 && json.method != undefined){
		let obj = {};
		
		// these are the token ID and hash for the server
		obj[cfg[0]] = cfg[1];
		obj['json'] = JSON.stringify(json);
		$.ajax({
			method: "POST",
			url: window.location.href.split("#")[0].split("?")[0] + "/handleAction",
			data: obj
		})
			.done(function(data) {
				if(json.type === TYPE.JOB){
					$("#msg_jobs").empty().append(
						"<div class='alert alert-success alert-dismissable fade in'>" +
							"<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" +
							"<strong>הצלחה!</strong> הפעולה הושלמה בהצלחה." +
						"</div>"
					);
					hide_alert("#msg_jobs");
				}else{
					$("#msg_requests").empty().append(
						"<div class='alert alert-success alert-dismissable fade in'>" +
							"<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" +
							"<strong>הצלחה!</strong> הפעולה הושלמה בהצלחה." +
						"</div>" 
					);
					hide_alert("#msg_requests");
				}
				
				// update table
				(json.type === TYPE.JOB) ? getJobs() : getRequests();
			})
			.fail(function(data) {
				if(json.type === TYPE.JOB){
					$("#msg_jobs"),empty().append(
						"<div class='alert alert-danger alert-dismissable fade in'>" +
							"<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" +
							"<strong>" + data + "</strong> שגיאה בתהליך." +
						"</div>"
					);
					hide_alert("#msg_jobs");
				}else{
					$("#msg_requests").empty().append(
						"<div class='alert alert-danger alert-dismissable fade in'>" +
							"<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>" +
							"<strong>" + data + "</strong> שגיאה בתהליך." +
						"</div>"
					);
					hide_alert("#msg_requests");
				}
			});
	}
}

// UX - hide alert after action (.alert classes)
function hide_alert(element){
	setTimeout(function(){
		$(element).find(".alert").alert('close');
	}, 5000);
}

function timedEvent(){
	$.ajax({
			method: "GET",
			url: window.location.href.split("#")[0].split("?")[0] + "/timedEvent"
	});
}