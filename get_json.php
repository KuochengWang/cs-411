<?php
$mysqli = new mysqli("cpanel3.engr.illinois.edu", "funproject_funproject", "X5V-tfN-7Yh-nnb", "funproject_Database");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
  
session_start();

#$sql = "SELECT username AS Id, bankID as Color, balance AS R FROM `User`";

$sql = "SELECT username AS Id, bankID as Color, (SUM(b2) + b1) as R

FROM (SELECT User.username, User.balance as b1, User.bankID,

             Account.balance * IF(Account.type = 'LTC', 2, 1) * IF(Account.type = 'BTC', 3, 1) * IF(Account.type = 'ETH', 4, 1) as b2,

             Account.type

     FROM `User`

     JOIN Account ON Account.username = User.username) AS C

GROUP BY username";

$sql_links = "SELECT (SELECT username FROM Account where from_ = walletID) AS Source, (SELECT username FROM Account where to_ = walletID) AS Target, amount AS weight
FROM `Transaction`
WHERE transaction_type='User' AND from_ IN (SELECT walletID FROM Account) AND to_ IN (SELECT walletID FROM Account)";

$result = $mysqli->query($sql);
$result_links = $mysqli->query($sql_links);

$items = array();
$links = array();

$num_rows = $result->num_rows;

echo("<strong>Welcome, " . $_SESSION["username"] . "!<strong>");

while($row = $result->fetch_assoc()) {
    $items[] = $row;
}
while($row_links = $result_links->fetch_assoc()) {
    $links[] = $row_links;
}
 
#echo json_encode($links);
 
$j = json_encode($items);
$l = json_encode($links);
 
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Data Visualization</title>
    <style>

.node {
    fill: #ccc;
    stroke: #fff;
    stroke-width: 2px;
}

.link {
    stroke: #999;
    stroke-opacity: 0.6;
}

    </style>
</head>
<body>
    <script src='http://d3js.org/d3.v3.min.js'></script>
    <script>

var width = 1200,
    height = 900;

var nodes = <?php echo json_encode($items) ?>;

var links = <?php echo json_encode($links) ?>;

var edges = [];
links.forEach(function(e) {
    var sourceNode = nodes.filter(function(n) {
        return n.Id === e.Source;
    })[0],
        targetNode = nodes.filter(function(n) {
            return n.Id === e.Target;
        })[0];

    edges.push({
        source: sourceNode,
        target: targetNode,
        value: e.Value
    });
});

var svg = d3.select('body').append('svg')
    .attr('width', width)
    .attr('height', height);
    

var force = d3.layout.force()
    .size([width, height])
    .nodes(nodes)
    .links(edges);

force.linkDistance(350);

var link = svg.selectAll('.link')
    .data(edges)
    .enter().append('line')
    .attr('class', 'link');
    
function EdgeWidth(d){
    var abc = "1.5";
    if(d.Value == 1.535){
        return 5;
    }
    return parseFloat(abc);
}

var node = svg.selectAll('.node')
    .data(nodes)
    .enter().append('circle')
    .attr('class', 'node')
    .style("fill", circleColor)
    .call(force.drag);

function circleColor(d){
    if(d.Color == "0"){
        return "blue";
    }
    if(d.Color == "1"){
        return "green";
    }
    if(d.Color == "2"){
        return "orange";
    }
    if(d.Color == "3"){
        return "moccasin";
    }
    if(d.Color == "4"){
        return "purple";
    }
    if(d.Color == "5"){
        return "black";
    }
    if(d.Color == "6"){
        return "pink";
    }
    if(d.Color == "7"){
        return "gray";
    }
    if(d.Color == "8"){
        return "brown";
    }
    if(d == "0"){
        return "blue";
    }
    if(d == "1"){
        return "green";
    }
    if(d == "2"){
        return "orange";
    }
    if(d == "3"){
        return "moccasin";
    }
    if(d == "4"){
        return "purple";
    }
    if(d == "5"){
        return "black";
    }
    if(d == "6"){
        return "pink";
    }
    if(d == "7"){
        return "gray";
    }
    if(d== "8"){
        return "brown";
    }
	return "red";
}

var legend = svg.selectAll(".legend")
     .data(["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"])//hard coding the labels as the datset may have or may not have but legend should be complete.
     .enter().append("g")
     .attr("class", "legend")
     .attr("transform", function(d, i) { return "translate(0," + i * 35 + ")"; });

// draw legend colored rectangles
legend.append("rect")
     .attr("x", width - 35)
     .attr("width", 30)
     .attr("height", 30)
     .style("fill",circleColor);

// draw legend text
legend.append("text")
     .attr("x", width - 40)
     .attr("y", 9)
     .attr("dy", ".45em")
     .style("text-anchor", "end")
     .text(function(d){return d});

force.on("tick", function() {

    node.attr('r', width/150)
        .attr('cx', function(d) { return d.x; })
        .attr('cy', function(d) { return d.y; })
        .attr("r", circleRad);

    link.attr('x1', function(d) { return d.source.x; })
        .attr('y1', function(d) { return d.source.y; })
        .attr('x2', function(d) { return d.target.x; })
        .attr('y2', function(d) { return d.target.y; })
        .attr("style", edgeWidth);

});

function circleRad(d){
    return Math.log(d.R) * 1.2;
    if(d.R == 0){
        return 1;
    }
    if(d.R > 2000){
        return 10;
    }
	return d.R / 100 /2;
}

function edgeWidth(d){
    return d.Value + 100;
}


force.start();

</script>
</body>
</html>