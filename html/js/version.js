$.ajax({
  url:"version.txt",
  success: function (data){;
    console.log(data.split('\n').shift());
    $("#version-tag").append("[" + data.split('\n').shift() + "]");
  }
});
