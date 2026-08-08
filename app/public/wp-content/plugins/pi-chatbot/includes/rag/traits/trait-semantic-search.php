<?php
/**
 * Retriever — Semantic search stub interface for future vector DB / Qdrant integration.
 *
 * @package PiChatbot
 */

if (!defined('ABSPATH')) exit;

trait Pi_Chatbot_Retriever_Semantic {
    /**
     * Interface cho semantic search — stub hiện tại, 
     * tích hợp Qdrant/Embedding API trong tương lai.
     *
     * @param string $question
     * @param int    $top_k
     * @return array{rows: array, status: string}
     */
    public static function search_semantic($question, $top_k = 10) {
        // Interface ready for vector database (e.g. Qdrant / OpenAI Embeddings)
        return [
            'rows'   => [],
            'status' => 'not_implemented'
        ];
    }
}
