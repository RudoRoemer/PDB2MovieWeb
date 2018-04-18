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
    	console.log(data);
    	subRes = JSON.parse(data);
    },
    cache: false,
    contentType: false,
    processData: false
	});

  return false;

}, false);
