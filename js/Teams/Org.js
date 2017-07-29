google.charts.load('current', {packages:["orgchart"]});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {

	var jsonData = $.ajax({
		url: "team/OrgJson",
		dataType: "json",
		async: false
	}).responseText;

	var data = new google.visualization.DataTable(jsonData);

	var options = {
		title: 'ADR',
		width: data.getNumberOfRows() * 65,
		bar: {groupWidth: 20},
		allowHtml:true,
		allowCollapse:true
	};

	// Create the chart.
	var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
	// Draw chart
	chart.draw(data, options);
}
