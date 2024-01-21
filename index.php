<?php
error_reporting(E_ALL ^ E_DEPRECATED);

use Jcupitt\Vips\BlendMode;
use Jcupitt\Vips\Image;

require __DIR__ . '/vendor/autoload.php';


// parse request
$data = file_get_contents('php://input');//file_get_contents('data.json');
$columns = [];
foreach (json_decode($data, 1) as $column) {
    $columns[] = new \Matt\Vips\Contracts\Column(
        type: $column['type'],
        rows: $column['rows'],
    );
}


// get all unique urls
$urlSet = [];
foreach ($columns as $column) {
    $urlSet = array_merge($urlSet, $column->getUrlSet());
}
$urlSet = array_unique($urlSet);

// download and cache or retrieve from cache
$images = [];
$scale = null;
foreach ($urlSet as $path) {
    $encodedName = __DIR__ . '/cache/' . base64_encode($path);
    $file = null;
    if (file_exists($encodedName)) {
        $file = file_get_contents($encodedName);
    } else {
        $file = file_get_contents($path);
        file_put_contents($encodedName, $file);
    }
    $images[$path] = Image::newFromBuffer($file);
    $scale = $scale ?: 2000 / $images[$path]->height;
    $images[$path] = $images[$path]->resize($scale);
}

// build each column
$composites=[];
foreach ($columns as $i => $column) {
    $composites[$i] = $images[$column->rows[0]->path];
    for ($x = 1; $x < count($column->rows); $x++) {
        $composites[$i] = $composites[$i]->composite2(
            $images[$column->rows[$x]->path],
            BlendMode::OVER,
            ['x' => 0, 'y' => round($column->rows[$x]->yOffset * $scale)]
        );
    }
}

// merge columns
$merged = $composites[0];
for($i = 1; $i < count($composites); $i++) {
    $merged = $merged->join($composites[$i], \Jcupitt\Vips\Direction::HORIZONTAL, []);
}

header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
header("Cache-Control: public");
header("Content-Type: image/png");
header("Content-Transfer-Encoding: Binary");
$time = time();
header("Content-Disposition: attachment; filename=merged-$time.png");
echo $merged->writeToBuffer('.png');
