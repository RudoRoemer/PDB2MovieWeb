var form = document.querySelector("form");

form.addEventListener("submit", function (e) {

  // Prevents the standard submit event
	e.preventDefault();

	var result = "Success";
	if (result !== "Success") {
		throw(result);
	}

	var fData = new FormData(this);
  // Optional. Append custom data.
  fData.append("email", $("#email_form").val());

	$.ajax({
    url: "/php/review.php",
	  type: 'POST',
    data: fData,
    async: true,
    success: function (data) {
    	var subRes = JSON.parse(data);
			for (i =0; i<subRes.length; i++) {
				$("#response-tables").append('<thead>' +
			    															'<tr>' +
																			    '<th scope="col">Python used?</th>' +
																					'<th scope="col">Resolution</th>' +
																			    '<th scope="col">Combi</th>' +
																			    '<th scope="col">Multi</th>' +
																					'<th scope="col">Waters</th>' +
																			    '<th scope="col">Threed</th>' +
																			    '<th scope="col">Confs<th>' +
																			    '<th scope="col">Freq</th>' +
																					'<th scope="col">Step</th>' +
																			    '<th scope="col">D. Step</th>' +
																			    '<th scope="col">Keep List</th>' +
																			    '<th scope="col">Modes</th>' +
																					'<th scope="col">Cutoffs</th>' +
																		  	'</tr>' +
																			'</thead')
															.append('<p>test</p>');

				console.log(subRes[i]);
			};
    },
    cache: false,
    contentType: false,
    processData: false
	});

  return false;

}, false);
