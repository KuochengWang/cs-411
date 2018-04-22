<?php
$mysqli = new mysqli("cpanel3.engr.illinois.edu", "funproject_funproject", "X5V-tfN-7Yh-nnb", "funproject_Database");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
  
session_start();

$sql = "SELECT username AS Id, bankID AS group FROM `User`";

$sql_links = "SELECT (SELECT username FROM Account where from_ = walletID) AS Source, (SELECT username FROM Account where to_ = walletID) AS Target, amount AS Value 
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
 
#var_dump($items);
 
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
    stroke: #777;
    stroke-width: 2px;
}

    </style>
</head>
<body>
    <script src='http://d3js.org/d3.v3.min.js'></script>
    <script>

var width = 1000,
    height = 1000;

/*var nodes = [
    { x:   width/3, y: height/2 },
    { x: 2*width/3, y: height/2 }
];*/
var nodes = <?php echo json_encode($items) ?>;

var links = <?php echo json_encode($links) ?>;
var color = d3.scaleOrdinal(d3.schemeCategory20);
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

force.linkDistance(width/2);

var link = svg.selectAll('.link')
    .data(edges)
    .enter().append('line')
    .attr('class', 'link');

var node = svg.selectAll('.node')
    .data(nodes)
    .enter().append('circle')
    .attr('class', 'node')
    .attr("fill", function(d) {return color(d.group);});

force.on('end', function() {

    node.attr('r', width/150)
        .attr('cx', function(d) { return d.x; })
        .attr('cy', function(d) { return d.y; }));

    link.attr('x1', function(d) { return d.source.x; })
        .attr('y1', function(d) { return d.source.y; })
        .attr('x2', function(d) { return d.target.x; })
        .attr('y2', function(d) { return d.target.y; });

});

force.start();

/*d3.select("body").selectAll("p")
            .data(dataset)
            .enter()
            .append("p")
            .text(function(d) { return d.username });*/
            


</script>
</body>
</html>

