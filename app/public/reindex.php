<?php
require 'wp-load.php';
echo "Reindexing... ";
$count = Pi_Chatbot_Indexer::reindex_all();
echo "Done! Indexed " . $count . " posts.\n";
