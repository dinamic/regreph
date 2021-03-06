<?php

/**
 * bench.php <testcase> <dir>
 */

require_once(__DIR__.'/../vendor/autoload.php');
require_once($argv[2].'/vendor/autoload.php');

$tests = \Regreph\TestCaseFinder::fromFile($argv[1]);
$output = array();
$runs = array();

foreach($tests as $test) {
  foreach($test->getTestMethods() as $method) {
    $result = $test->run($method, 25);
    $runs []= $result->runId;
    $output ['results'][$method] = array(
      'runId' => $result->runId,
      'totals' => $result->totals
    );
  }
}

// rollup all test method runs into one run

$diskRuns = new \XHProfRuns_Default();
$rollup = xhprof_aggregate_runs($diskRuns, $runs, array_fill(0, count($runs), 1), 'regreph');
$rollupId = $diskRuns->save_run($rollup['raw'], 'regreph_rollup');

$output['rollUp'] = $rollupId;
$output['timestamp'] = date('r');

if(in_array('--json', $argv)) {
  echo json_encode($output);
} else {
  print_r($output);
}



