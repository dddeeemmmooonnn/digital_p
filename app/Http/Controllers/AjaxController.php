<?php

namespace App\Http\Controllers;

use App\Map;
use App\Container;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    private function getDistance($map, $first, $second) {
        $visited = $map;
        $target = $map[$second['x']][$second['y']];
        $start = $map[$first['x']][$first['y']];
        if ($target < 0 || $start < 0) {
            return false;
        }

        foreach ($visited as &$row) {
            foreach ($row as &$item) {
                $item = ($item === 0 || $item === $target || $item === $start) ? 0 : -1 ;
            }
        }

        $queue[] = $first;
        while (count($queue) > 0) {
            $curr = array_shift($queue);
            $curr_d = $visited[$curr['x']][$curr['y']];
            if ($curr === $second) {
                return $curr_d;
            }
            $visited[$curr['x']][$curr['y']] = -1;
            if ($visited[$curr['x'] + 1][$curr['y']] === 0) {
                $queue[] = ['x' => $curr['x'] + 1, 'y' => $curr['y']];
                $visited[$curr['x'] + 1][$curr['y']] = $curr_d + 1;
            }
            if ($visited[$curr['x']][$curr['y'] + 1] === 0) {
                $queue[] = ['x' => $curr['x'], 'y' => $curr['y'] + 1];
                $visited[$curr['x']][$curr['y'] + 1] = $curr_d + 1;
            }
            if ($visited[$curr['x'] - 1][$curr['y']] === 0) {
                $queue[] = ['x' => $curr['x'] - 1, 'y' => $curr['y']];
                $visited[$curr['x'] - 1][$curr['y']] = $curr_d + 1;
            }
            if ($visited[$curr['x']][$curr['y'] - 1] === 0) {
                $queue[] = ['x' => $curr['x'], 'y' => $curr['y'] - 1];
                $visited[$curr['x']][$curr['y'] - 1] = $curr_d + 1;
            }

        }
        return false;
    }

    private function nextSet(&$a, $n) {
        $j = $n - 2;
        while ($j != -1 && $a[$j] >= $a[$j + 1])
            $j--;
        if ($j == -1)
            return false; // больше перестановок нет
        $k = $n - 1;
        while ($a[$j] >= $a[$k])
            $k--;
        swap($a[$j], $a[$k]);
        $l = $j + 1;
        $r = $n - 1; // сортируем оставшуюся часть последовательности
        while ($l < $r)
            swap($a[$l++], $a[$r--]);
        return true;
    }

    public function getWay(Request $request, $id)
    {
        $m = [
            [-1, -1, -1, -1, -1, -1],
            [-1, -1, 0, -1, -1, -1],
            [-1, 1, 0, -1, 2, -1],
            [-1, -1, 0, 0, 0, -1],
            [-1, -1, 0, -1, 3, -1],
            [-1, -1, 0, 4, -1, -1],
            [-1, -1, 0, 5, -1, -1],
            [-1, -1, -1, -1, -1, -1],
        ];

        $way = [
            ['x' => 1, 'y' => 2],
            ['x' => 4, 'y' => 4],
            ['x' => 2, 'y' => 1],
            ['x' => 5, 'y' => 3],
            ['x' => 2, 'y' => 4],
            ['x' => 6, 'y' => 3],
            ['x' => 6, 'y' => 2],
        ];

        $start = array_shift($way);
        $end = array_pop($way);

        $start_d = array_map(function ($value) use ($start, $m) {
            return $this->getDistance($m, $start, $value);
        }, $way);
        $end_d = array_map(function ($value) use ($end, $m) {
            return $this->getDistance($m, $end, $value);
        }, $way);

        $way_d = [];
        foreach ($way as $i => $first) {
            foreach ($way as $j => $second) {
                if ($i === $j) {
                    $way_d[$i][$j] = 0;
                } elseif ($i < $j) {
                    $way_d[$i][$j] = $this->getDistance($m, $first, $second);
                } else {
                    $way_d[$i][$j] = $way_d[$j][$i];
                }
            }
        }
        $a = [];
        $n = count($way_d);
        for ($i = 0; $i < $n; $i++)
            $a[$i] = $i;

        $min = PHP_INT_MAX ;
        $ans = false;
        while ($this->nextSet($a, $n)) {
            $t = $start_d[$a[0]] + $end_d[$a[$n - 1]];
            for ($i = 1; $i < $n; $i++) {
                $t += $way_d[$a[$i - 1]][$a[$i]];
            }
            if ($t < $min) {
                $ans = $a;
                $min = $t;
            }
        }
        $way_ans = array_map(function ($value) use ($way) {
            return $way[$value];
        }, $ans);
        array_unshift($way_ans, $start);
        array_push($way_ans, $end);

        foreach ($way_ans as $i => $step) {
            $m[$step['x']][$step['y']] = $i;
        }
//        echo $min . ' ' . implode(', ', $ans) . '<br>';
//        echo '<pre>';
//        print_r($start_d);
//        die;

        return
//            response()->json($way_ans);
            view('map', ['map' => $m, 'way_d' => $way_d, 'way_ans' => $way_ans]);

    }

    public function getMap(Request $request, $id)
    {
        $map = Map::findOrFail($id);

        $m = json_decode($map->map, true);

//        $m = [
//            [-1, -1, -1, -1, -1, -1],
//            [-1, -1, 0, -1, -1, -1],
//            [-1, 1, 0, -1, 2, -1],
//            [-1, -1, 0, 0, 0, -1],
//            [-1, -1, 0, -1, 3, -1],
//            [-1, -1, 0, 4, -1, -1],
//            [-1, -1, 0, 5, -1, -1],
//            [-1, -1, -1, -1, -1, -1],
//        ];

        $return = [];
        array_walk_recursive($m, function ($value) use (&$return) {
            if ($value > 0) {
                $return[] = $value;
            }
        });

        $containers = Container::find($return);

        return response()->json([
            'map' => $m,
            'containers' => $containers->toArray(),
        ]);
    }

    public function createMap(Request $request)
    {
        $m = json_decode($request->map, true);
//        $m = [
//            [-1, -1, -1, -1, -1, -1],
//            [-1, -1, 0, -1, -1, -1],
//            [-1, 1, 0, -1, 1, -1],
//            [-1, -1, 0, 0, 0, -1],
//            [-1, -1, 0, -1, 1, -1],
//            [-1, -1, 0, 1, -1, -1],
//            [-1, -1, 0, 1, -1, -1],
//            [-1, -1, -1, -1, -1, -1],
//        ];

        $return = [];
        array_walk_recursive($m, function (&$value) use (&$return) {
            if ($value > 0) {
                $return[] = $value;
                $c = new Container;
                $c->save();
                $value = $c->id;
            }
        });

        $map = new Map;
        $map->map = json_encode($m);
        $map->save();

        return response('ok');
    }
}
