<button id="getViews">Authorize</button>
<h1>Hello Analytics</h1>
<textarea cols="80" rows="20" id="query-output"></textarea>
<script>
  // Replace with your client ID from the developer console. https://console.developers.google.com/apis/credentials
  var CLIENT_ID = '323959956891-94q7h3ducvfto4bu150c60eitti96gna.apps.googleusercontent.com';
  // Replace with your view ID. from https://ga-dev-tools.appspot.com/account-explorer/
  var VIEW_ID = 'ga:140941321';
  var DISCOVERY = 'https://analyticsreporting.googleapis.com/$discovery/rest';
  var SCOPES = ['https://www.googleapis.com/auth/analytics.readonly'];
  function authorize(event) {
    // Handles the authorization flow.
    // `immediate` should be false when invoked from the button click.
    var useImmdiate = event ? false : true;
    var authData = {
      client_id: CLIENT_ID,
      scope: SCOPES,
      immediate: useImmdiate
    };
    gapi.auth.authorize(authData, function(response) {
      if (response.error) {
  $("#query-output").text("인증필요");
      }
      else {
  $("#query-output").text("불러오는 중");
        queryReports();
      }
    });
  }
  function queryReports() {
    // Load the API from the client discovery URL.
    gapi.client.load(DISCOVERY
    ).then(function() {
        // Call the Analytics Reporting API V4 batchGet method.
        gapi.client.analyticsreporting.reports.batchGet( {
          "reportRequests":
              [
                {
                  "viewId": "ga:140941321",
                  "dateRanges": [{"startDate": "2017-01-01", "endDate": "2017-02-25"}],
                  "metrics": [{"expression": "ga:users"}],
                  "dimensions": [{"name": "ga:city"}]
                }
              ]
        } ).then(function(response) {
          var parse = JSON.parse(response.body);
          var views = "결과값: "+parse.reports[0].data.totals[0].values[0];
          console.log(parse);
    $("#query-output").text(views);
        })
        .then(null, function(err) {
            // Log any errors.
            console.log(err);
        });
    });
  }
 $("#getViews").click(function(){authorize(event);});
</script>
<script async src="https://apis.google.com/js/client.js"></script>
