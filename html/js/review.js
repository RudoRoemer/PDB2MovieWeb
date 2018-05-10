var subRes;

function removeReq(entry) {

  var fDataDel = new FormData();

  fDataDel.append("filename", subRes[entry].filename);
  fDataDel.append("reqID", subRes[entry].req_id);

  $.ajax({
    url: "/php/delete.php",
    type: 'POST',
    data: fDataDel,
    async: true,
    'beforeSend': function() {
      $('#loading_gif' + entry).show();
      $("#process").prop("disabled", true);
      $("#tos").prop("disabled", true);
    },
    success: function(data) {
      console.log(data);
      $('#loading_gif' + entry).hide();
      $("#entryTitle" + entry).remove()
      $("#newTable" + entry).remove();
      $("#removeButton" + entry).remove();
    },
    failure: function(data) {
      console.log("NOPE");
    },
    'complete': function() {
    },
    cache: false,
    contentType: false,
    processData: false
  });
}

var form = document.querySelector("form");

form.addEventListener("submit", function(e) {

  // Prevents the standard submit event
  e.preventDefault();
  $(".toRemove").remove()

  var result = "Success";
  if (result !== "Success") {
    throw (result);
  }

  var fData = new FormData(this);

  var codePatt = new RegExp(/[0-9][0-9][0-9][0-9][0-9][0-9]/);
  var emailPatt = new RegExp(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);

  var error;
  if (!codePatt.test($("#code_form").val())) {
    error = "Invalid secret code.";
  }
	if (!emailPatt.test($("#email_form").val())) {
    error = "Invalid email.";
  }

  if (error != null) {
    $("#response-tables").append("" +
      "<h2 class='toRemove'>Something has gone wrong.</h2>" +
      "<p class='toRemove'>" + error + "</p>"
    );
    return
  }

  fData.append("email", $("#email_form").val());
  fData.append("secret_code", parseInt($("#code_form").val()));

  $.ajax({
    url: "/php/review.php",
    type: 'POST',
    data: fData,
    async: true,
    success: function(data) { //I'm so sorry you have to look at this. I'm learning angular, but for now, enjoy my JQuery bodge job.
      console.log(data);
      subRes = JSON.parse(data);
      var completedFlag = true;
      $("#response-tables").empty();
      if (subRes[0].complete == 0) {
        $("#response-tables").append("<br><h2 class='bubble' style='background-color: #EEE; border-color:#EEE; padding-left: 20px;padding-top:20px; padding-bottom: 20px;>Current Requests</h1>");
        completedFlag = false;
      } else {
        $("#response-tables").append("<br><h2 class='bubble' style='background-color: #EEE; border-color:#EEE; padding-left: 20px;padding-top:20px; padding-bottom: 20px;'>Your Historical Requests</h1>");
      }
      for (i = 0; i < subRes.length; i++) {
        if (subRes[i].complete == 1) {
          var fstLine = '<hr><h3 id="entryTitle' + i + '">' + subRes[i].original_name + ' -</h2><br><a href="https://pdb2movie.warwick.ac.uk/download/'+ subRes[i].filename +'.zip">https://pdb2movie.warwick.ac.uk/download/' + subRes[i].filename + '.zip</a>';
        } else {
          var fstLine = '<hr><h3 id="entryTitle' + i + '" style="position: inherit">' + subRes[i].original_name + ' - </h2><button type="button" class="btn btn-dark" id="removeButton'+ i +'" onClick="removeReq(' + i + ')" style="float: right;">Remove</button><img id="loading_gif' + i +'" height="15" width="15" style="float: right;" src="../img/loading.gif" />';
        }
        $("#response-tables").append("" +
          fstLine +
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
          '</thead>');
        $("#newTable" + i).append("" +
          '<tbody>' +
          '<td>' + ((subRes[i].python_used) ? "Yes" : "No") + '</td>' +
          '<td>' + subRes[i].resolution + '</td>' +
          '<td>' + ((subRes[i].combi) ? "Yes" : "No") + '</td>' +
          '<td>' + ((subRes[i].multi) ? "Yes" : "No") + '</td>' +
          '<td>' + ((subRes[i].waters) ? "Yes" : "No") + '</td>' +
          '<td>' + ((subRes[i].threed) ? "Yes" : "No") + '</td>' +
          '<td>' + subRes[i].confs + '</td>' +
          '<td>' + subRes[i].freq + '</td>' +
          '<td>' + subRes[i].step + '</td>' +
          '<td>' + subRes[i].dstep + '</td>' +
          '<td>' + ((subRes[i].molList === "NULL") ? "None" : subRes[i].molList) + '</td>' +
          '<td>' + subRes[i].modList + '</td>' +
          '<td>' + subRes[i].cutList + '</td>' +
          '</tbody>' +
          '</table>');
        //console.log(subRes[i]);
        $("#loading_gif" + i).hide();
        if (completedFlag != true && subRes.length > i+1) {
          if (subRes[i+1].complete == 1) {
            completedFlag = true;
            $("#response-tables").append("<br><h1 class='bubble' style='background-color: #EEE; border-color:#EEE; padding-left: 20px;padding-top:20px; padding-bottom: 20px; margin-top:40px;'>Your Historical Requests</h1><hr>");
          }
        }
      };
      if (subRes.status === "failure") {
        $("#response-tables").append("" +
          "<h2 class='toRemove'>" + subRes.title + "</h2>" +
          "<p class='toRemove'>" + subRes.text + "</p>"
        );
      }
    },
    cache: false,
    contentType: false,
    processData: false
  });

  return false;

}, false);
