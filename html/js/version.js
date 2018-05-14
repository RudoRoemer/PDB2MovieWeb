var forkName = 'https://github.com/RudoRoemer';

$.ajax({
  url:"version.txt",
  success: function (data){;
    res = data.split('\n').shift();
    console.log(res);
    $("#version-tag").append("[" + data.split('\n').shift() + "]").attr("href", forkName + '/PDB2MovieWeb/commit/' + res.substr(-7));
  }
});
