import { defineStore } from 'pinia'
import { ref } from 'vue'

export interface Document {
    id: number
    title: string
    description: string
    size: number
    downloads: number
    upload_date: string
    author: string
    tags: string[]
    path: string
}

export const useDocumentStore = defineStore('documents', () => {
    const documents = ref<Document[]>([
        {
        id: 1,
        title: 'Mémoire sur le data mining',
        description: 'Étude approfondie sur les avancées récentes en IA',
        downloads: 50,
        size: 2.4,
        upload_date: '2025-01-15',
        author: 'Abraham AHOUANYIZOUN',
        tags: ['IA', 'Recherche', 'Technologie'],
        path: 'https://sharedoc.com/memoire-ia.pdf'
        },
        {
        id: 2,
        title: 'Mémoire sur l\'intelligence artificielle',
        description: 'Étude approfondie sur les avancées récentes en IA',
        downloads: 0,
        size: 1.8,
        upload_date: '2025-02-20',
        author: 'Olabissi GBANGBOCHE',
        tags: ['IA', 'Recherche', 'Technologie'],
        path: 'https://sharedoc.com/memoire-ia.pdf'
        },
        {
        id: 3,
        title: 'Mémoire sur la machine learning',
        description: 'Étude approfondie sur les avancées récentes en IA',
        downloads: 17,
        size: 3.2,
        upload_date: '2025-03-05',
        author: 'Emmanuel GBAVOU',
        tags: ['IA', 'Recherche', 'Technologie'],
        path: 'https://sharedoc.com/memoire-ia.pdf'
        }
    ])

    function getDocumentById(id: number) {
        return documents.value.find(doc => doc.id === id)
    }

    return {
        documents,
        getDocumentById
    }
})