<?php

/**
 * @OA\Info(
 *     title="API Bancaire Laravel",
 *     version="1.0.0",
 *     description="Documentation complète de l'API bancaire avec gestion des comptes, transactions et blocages automatiques",
 *     @OA\Contact(
 *         email="contact@banque.example.com"
 *     )
 * )
 *
 * // Force regeneration for Render deployment
 *
 * @OA\Server(
 *     url="https://proget-laravel-api.onrender.com/api/v1",
 *     description="Serveur de production Render"
 * ),
 * @OA\Server(
 *     url="http://localhost:8000/api/v1",
 *     description="Serveur de développement"
 * ),
 * @OA\Server(
 *     url="/api/v1",
 *     description="Serveur relatif"
 * )
 *
 * @OA\Tag(
 *     name="Comptes",
 *     description="Gestion des comptes bancaires"
 * )
 */