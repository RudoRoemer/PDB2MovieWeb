var renderer

$("#main").load("test.html")

$.ajax({
  url:"php/rendererInfo.php",
  type: 'POST',
  async: true,
  success: function (data){
    console.log(data);
    res = JSON.parse(data);
    renderer = res.renderer;
    pageConstructor();
  },
  failure: function (data){
    renderer = "vmd";
    pageConstructor();
  },
  timeout: 10000,
  cache: false,
  contentType: false,
  processData: false
});

function pageConstructor() {

  switch (renderer) {
    case "vmd":
      fillUploadAttr("Please upload .tcl Video File:", ".tcl");
    case "pymol":
      fillUploadAttr("Please upload .py Video File:", ".py");
    case "both":
      if (res.pymolAllowed) {
        fillUploadAttr("Please upload .py or .tcl Video File:", ".py,.tcl")
      } else {
        fillUploadAttr("Please upload .tcl Video File:", ".tcl");
      }
    default:
      $("#pyFileText").empty();
      $("#pyFileText").append("Please upload .tcl Video File:");
      $("#pyFile").attr("accept",".tcl");
  }

}

function fillUploadAttr(text, exts) {
  $("#pyFileText").empty();
  $("#pyFileText").append(text);
  $("#pyFile").attr("accept", exts);
}
