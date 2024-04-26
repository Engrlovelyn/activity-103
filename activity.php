<?php

// Function to search for a book and handle borrowing process
function borrowBook($bookTitle, $borrowerId, &$inventory, &$transactions) {
    // Check if the book is available in inventory
    if (isset($inventory[$bookTitle]) && $inventory[$bookTitle]['copies'] > 0) {
        // Record borrower's ID and update inventory
        $inventory[$bookTitle]['copies'] -= 1;
        
        // Create insurance transaction
        $transactionId = generateTransactionId();
        $transactions[$transactionId] = [
            'book_title' => $bookTitle,
            'borrower_id' => $borrowerId,
            'transaction_type' => 'borrow',
            'transaction_date' => date('Y-m-d H:i:s')
        ];

        return "Book '$bookTitle' its copy is available.";
    } else {
        return "Sorry, the book '$bookTitle' its copy is not available.";
    }
}

// Function to generate a unique transaction ID
function generateTransactionId() {
    return uniqid('TRAN_', true);
}

// Example usage
$inventory = [
    'Book vince story' => ['copies' => 3],
    'Book game of thrones' => ['copies' => 4],
    'Book harry potter' => ['copies'=> 4],
    // Add more books to inventory
];

$transactions = [];

// Example borrowing process
$bookTitle = 'Book game of thrones';
$borrowerId = '123456';
echo borrowBook($bookTitle, $borrowerId, $inventory, $transactions);


// Function to handle returning book copies
function returnBookCopy($bookTitle, $borrowerId, $returnTime, &$inventory, &$transactions) {
    // Check if the book copy is overdue
    $borrowTransaction = findBorrowTransaction($bookTitle, $borrowerId, $transactions);
    if ($borrowTransaction) {
        $borrowDateTime = strtotime($borrowTransaction['transaction_date']);
        $returnDateTime = strtotime($returnTime);
        $hoursDifference = round(($returnDateTime - $borrowDateTime) / (60 * 60), 2);

        // Check if book copy is returned within 24 hours
        if ($hoursDifference <= 24) {
            // Book returned within 24 hours, no penalty
            $message = "Book copy of '$bookTitle' returned within 24 hours.";
        } else {
            // Apply penalty of 5 pesos for late return
            $borrowTransaction['penalty'] = 5;
            $transactions[] = $borrowTransaction;
            $message = "Book copy of '$bookTitle' returned late. Penalty of 5 pesos applied.";
        }

        // Update inventory
        if (isset($inventory[$bookTitle])) {
            $inventory[$bookTitle]['copies'] += 1;
        } else {
            $inventory[$bookTitle] = ['copies' => 1];
        }

        return $message;
    } else {
        return "No borrow transaction found for book copy of '$bookTitle' and borrower ID '$borrowerId'.";
    }
}

// Function to find the borrow transaction for a book copy
function findBorrowTransaction($bookTitle, $borrowerId, $transactions) {
    foreach ($transactions as $transaction) {
        if ($transaction['book_title'] === $bookTitle && $transaction['borrower_id'] === $borrowerId && $transaction['transaction_type'] === 'borrow') {
            return $transaction;
        }
    }
    return null;
}

// Example usage
$inventory = [
    'Book vince story' => ['copies' => 3],
    'Book game of thrones' => ['copies' => 4],
    'Book harry potter' => ['copies'=> 4],
    // Add more books to inventory
];

$transactions = [
    ['book_title' => 'Book game of thrones', 'borrower_id' => '123456', 'transaction_type' => 'borrow', 'transaction_date' => '2024-04-25 10:00:00'],
    // Add more transactions
];

// Example return process
$bookTitle = 'Book game of thrones';
$borrowerId = '123456';
$returnTime = '2024-04-26 10:00:00';
echo returnBookCopy($bookTitle, $borrowerId, $returnTime, $inventory, $transactions);

?>
