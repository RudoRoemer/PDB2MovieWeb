var subRes;
var offSet = 0;
var email;
var secret;
var form = document.querySelector("form");

form.addEventListener("submit", function(e) {

  // Prevents the standard submit event
  e.preventDefault();
  $(".toRemove").remove()
  $("#response-tables").empty();

  var result = "Success";
  if (result !== "Success") {
    throw (result);
  }

  var codePatt = new RegExp(/[0-9][0-9][0-9][0-9][0-9][0-9]/);
  var emailPatt = new RegExp(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);

  email = $("#email_form").val()
  code = $("#code_form").val()

  var error;
  if (!codePatt.test(code)) {
    error = "Invalid secret code.";
  }
  if (!emailPatt.test(email)) {
    error = "Invalid email.";
  }

  if (error != null) {
    $("#response-tables").append("" +
      "<h2 class='toRemove'>Something has gone wrong.</h2>" +
      "<p class='toRemove'>" + error + "</p>"
    );
    return
  }

  offSet = 0
  sendReq();

  return false;

}, false);

function nudgeList(nudge) {

  offSet += nudge
  $("#response-tables").empty();
  sendReq();
}

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
    'complete': function() {},
    cache: false,
    contentType: false,
    processData: false
  });
}

function sendReq() {

  var fData = new FormData();
  fData.append("email", $("#email_form").val());
  fData.append("secret_code", parseInt($("#code_form").val()));
  fData.append("offset", offSet);

  $.ajax({
    url: "/php/review.php",
    type: 'POST',
    data: fData,
    async: true,
    success: function(data) { //I'm so sorry you have to look at this. I'm learning angular, but for now, enjoy my JQuery bodge job.

      console.log(data);
      subRes = JSON.parse(data);
      var completedFlag = true;

      if (subRes.length > 0) {
        if (subRes[0].complete == 0) {
          console.log("baddadadd");
          $("#response-tables").append("<br><h1 class='bubble' style='background-color: #EEE; border-color:#EEE; padding-left: 20px;padding-top:20px; padding-bottom: 20px;'>Current Requests</h1><hr>");
          completedFlag = false;
        } else {
          $("#response-tables").append("<br><h1 class='bubble' style='background-color: #EEE; border-color:#EEE; padding-left: 20px;padding-top:20px; padding-bottom: 20px;'>Your Historical Requests</h1><hr>");
        }

        for (i = 0; i < subRes.length; i++) {

          var date = new Date(subRes[i].timestamp*1000).toLocaleString('en-GB', { timeZone: 'UTC' });

          if (subRes[i].complete == 1) {
            var extension = (subRes[i].extension !== null ? subRes[i].extension : ".zip");
            var fstLine = '<hr><h3 id="entryTitle' + i + '">' + subRes[i].original_name + ' - '+ date +'</h2><br><a href="https://pdb2movie.warwick.ac.uk/download/' + subRes[i].filename + extension +'">https://pdb2movie.warwick.ac.uk/download/' + subRes[i].filename + extension + '</a>';
          } else {
            var fstLine = '<hr><h3 id="entryTitle' + i + '" style="position: inherit">' + subRes[i].original_name + ' - '+ date +' </h2><button type="button" class="btn btn-dark" id="removeButton' + i + '" onClick="removeReq(' + i + ')" style="float: right;">Remove</button><img id="loading_gif' + i + '" height="15" width="15" style="float: right;" src="../img/loading.gif" />';
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
            '<th scope="col">Confs</th>' +  //4 Weeks, 1 Day, 2 Hours, 20 Minutes
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

            if (subRes[i].time_start !== 0) {
              var dateStart = new Date(subRes[i].time_start*1000).toLocaleString('en-GB', { timeZone: 'UTC' });
              $("#response-tables").append("<p>Request began processing at: "+dateStart+"</p>");
            }
            if (subRes[i].time_comp !== 0) {
              var dateComp = new Date(subRes[i].time_comp*1000).toLocaleString('en-GB', { timeZone: 'UTC' });
              $("#response-tables").append("<p>Request finished processing at: "+dateComp+"</p>");
            }

          //console.log(subRes[i]);
          $("#loading_gif" + i).hide();
          if (completedFlag != true && subRes.length > i + 1) {
            if (subRes[i + 1].complete == 1) {

              completedFlag = true;
              $("#response-tables").append("<br><h1 class='bubble' style='background-color: #EEE; border-color:#EEE; padding-left: 20px;padding-top:20px; padding-bottom: 20px; margin-top:40px;'>Your Historical Requests</h1><hr>");

            }
          }
        };
      }

      if (subRes.status === "failure") {
        if (offSet === 0) {

          $("#response-tables").append("" +
            "<h2 class='toRemove'>" + subRes.title + "</h2>" +
            "<p class='toRemove'>" + subRes.text + "</p>"
          );

        } else {

          $("#response-tables").append("" +
            "<h2 class='toRemove'>End of List</h2>"
          );

        }
      }

      $("#response-tables").append('<div style="text-align:center">' +
        '<button type="button" class="btn btn-dark" id="offset_neg" onClick="nudgeList(-10)" style="text-align:center; padding: 1px 10px 1px 10px; margin-right:2px"><</button>' +
        '<button type="button" class="btn btn-dark" id="offset_pos" onClick="nudgeList(10)"  style="text-align:center; padding: 1px 10px 1px 10px; margin-left:2px">></button>' +
        '</div>');

      if (offSet <= 0) {
        $("#offset_neg").prop('disabled', true);
      }
      if (subRes.length === undefined) {
        $("#offset_pos").prop('disabled', true);
      }

    },
    cache: false,
    contentType: false,
    processData: false
  });
}
