
<?php

require __DIR__ . '/vendor/autoload.php';

//https://github.com/vladimir163/lead-generator
//https://docs.google.com/document/d/12ia3kVyMn0WAaPzXOdRNbqKKE6YTx1MYwuELch7AaVw/edit#

//use Spatie\Async\Pool;

$tasks = range(1, 500);

// This loop creates a new fork for each of the items in $tasks.
foreach ($tasks as $task) {
    $pid = pcntl_fork();

    if ($pid == -1) {
        exit("Error forking...\n");
    }
    else if ($pid == 0) {
        execute_task($task);
        exit();
    }
}

// This while loop holds the parent process until all the child threads
// are complete - at which point the script continues to execute.
while(pcntl_waitpid(0, $status) != -1);

// You could have more code here.
echo "Do stuff after all parallel execution is complete.\n";

/**
 * Helper method to execute a task.
 */
function execute_task($task_id) {
    echo "Starting task: {$task_id}\n";

    // Simulate doing actual work with sleep().
    $execution_time = rand(5, 10);
    sleep($execution_time);

    echo "Completed task: {$task_id}. Took {$execution_time} seconds.\n";
}