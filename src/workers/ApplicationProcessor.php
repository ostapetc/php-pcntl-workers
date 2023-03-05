<?php

namespace Workers;

// extremely naive worker implementation - please do not use in production ^^

use LeadGenerator\Generator;
use LeadGenerator\Lead;

echo "[APPLICATIONS PROCESSOR] Running... Hit Ctrl+C to exit. \n";

$generator = new Generator();

$generator->generateLeads(10000, function (Lead $lead) {
    echo "Category $lead->id \n";
    echo "CategoryName $lead->categoryName \n";
});

echo "While(true)\n";

while (true) {
    echo "Do job at " . date("H:i:s") . "\n";

    $generator->generateLeads(10, function (Lead $lead) {
        echo "Category $lead->id \n";
        echo "CategoryName $lead->categoryName \n";
    });
}