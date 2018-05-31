$.ajax({
  url:"php/RemoteStatus.php",
  type: 'POST',
  async: true,
  success: function (data){
    console.log(data);
    res = JSON.parse(data);
    colour = (res.title == "Online" ? "green" : "red");
    $("#processing-status").empty();
    $("#processing-status").css("color", colour);
    $("#processing-status").append(res.title)
  },
  failure: function (data){
    console.log("FAI");
    res = JSON.parse(data);
    colour = "red"
    $("#processing-status").empty();
    $("#processing-status").css("color", colour);
    $("#processing-status").append(res.title)
  },
  timeout: 10000,
  cache: false,
  contentType: false,
  processData: false
});
