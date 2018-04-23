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
				$("#response-tables").append(	'<h2>' + subRes[i].filename + '</h2>' +
																			'<table class="table" id="newTable' + i + '">' +
																				'<thead>' +
				    															'<tr>' +
																				    '<th scope="col">Python used?</th>' +
																						'<th scope="col">Resolution</th>' +
																				    '<th scope="col">Combi</th>' +
																				    '<th scope="col">Multi</th>' +
																						'<th scope="col">Waters</th>' +
																				    '<th scope="col">Threed</th>' +
																				    '<th scope="col">Confs</th>' +
																				    '<th scope="col">Freq</th>' +
																						'<th scope="col">Step</th>' +
																				    '<th scope="col">D. Step</th>' +
																				    '<th scope="col">Keep List</th>' +
																				    '<th scope="col">Modes</th>' +
																						'<th scope="col">Cutoffs</th>' +
																			  	'</tr>' +
																				'</thead');
				$("#newTable" + i).append(
																			 	'<tbody>' +
																					'<td>' + ((subRes[i].python_used) ? "Yes" : "No" ) + '</td>' +
																					'<td>' + subRes[i].resolution + '</td>' +
																					'<td>' + ((subRes[i].combi) ? "Yes" : "No" ) + '</td>' +
																					'<td>' + ((subRes[i].multi) ? "Yes" : "No" ) + '</td>' +
																					'<td>' + ((subRes[i].waters) ? "Yes" : "No" ) + '</td>' +
																					'<td>' + ((subRes[i].threed) ? "Yes" : "No" ) + '</td>' +
																					'<td>' + subRes[i].confs + '</td>' +
																					'<td>' + subRes[i].freq + '</td>' +
																					'<td>' + subRes[i].step + '</td>' +
																					'<td>' + subRes[i].dstep + '</td>' +
																					'<td>' + (( subRes[1].molList === "NULL" ) ? "None" : subRes[1].molList ) + '</td>' +
																					'<td>' + subRes[1].modList + '</td>' +
																					'<td>' + subRes[1].cutList + '</td>' +
																				'</tbody>'+
																			'</table>'
															);

				console.log(subRes[i]);
			};
    },
    cache: false,
    contentType: false,
    processData: false
	});

  return false;

}, false);
