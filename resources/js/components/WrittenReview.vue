<template>
    <article
        class="written-review text-gray-300 text-sm leading-7"
        :class="{ 'max-h-80 overflow-hidden': isLong && !expanded }"
        v-html="renderedContent"
    ></article>
    <Button
        v-if="isLong"
        :label="expanded ? 'عرض أقل' : 'عرض المزيد'"
        severity="secondary"
        size="small"
        text
        class="mt-2"
        @click="expanded = !expanded"
    />
</template>

<script setup>
import { computed, ref } from "vue";
import Button from "primevue/button";
import DOMPurify from "dompurify";
import { marked } from "marked";

const props = defineProps({
    content: {
        type: String,
        default: "",
    },
});

const renderedContent = computed(() =>
    DOMPurify.sanitize(marked.parse(props.content || "")),
);
const isLong = computed(() => (props.content?.length || 0) > 1400);
const expanded = ref(false);
</script>

<style scoped>
.written-review :deep(h1),
.written-review :deep(h2),
.written-review :deep(h3),
.written-review :deep(h4),
.written-review :deep(h5),
.written-review :deep(h6) {
    color: #fbbf24;
    font-weight: 700;
    line-height: 1.4;
    margin-block: 1.25rem 0.5rem;
}

.written-review :deep(h1) {
    font-size: 1.5rem;
}

.written-review :deep(h2) {
    font-size: 1.25rem;
}

.written-review :deep(h3) {
    font-size: 1.125rem;
}

.written-review :deep(p),
.written-review :deep(ul),
.written-review :deep(ol),
.written-review :deep(blockquote),
.written-review :deep(pre),
.written-review :deep(table) {
    margin-block: 0.75rem;
}

.written-review :deep(ul) {
    list-style: disc;
    padding-inline-start: 1.5rem;
}

.written-review :deep(ol) {
    list-style: decimal;
    padding-inline-start: 1.5rem;
}

.written-review :deep(li + li) {
    margin-block-start: 0.25rem;
}

.written-review :deep(blockquote) {
    border-inline-start: 3px solid #fbbf24;
    color: #d1d5db;
    padding-inline-start: 1rem;
}

.written-review :deep(a) {
    color: #60a5fa;
    text-decoration: underline;
    text-underline-offset: 2px;
}

.written-review :deep(strong) {
    color: #f3f4f6;
    font-weight: 700;
}

.written-review :deep(em) {
    font-style: italic;
}

.written-review :deep(del) {
    text-decoration: line-through;
}

.written-review :deep(code) {
    background: #111827;
    border-radius: 0.25rem;
    color: #fcd34d;
    padding: 0.125rem 0.375rem;
}

.written-review :deep(pre) {
    background: #111827;
    border: 1px solid #374151;
    border-radius: 0.75rem;
    overflow-x: auto;
    padding: 1rem;
}

.written-review :deep(pre code) {
    background: transparent;
    padding: 0;
}

.written-review :deep(table) {
    border-collapse: collapse;
    display: block;
    max-width: 100%;
    overflow-x: auto;
}

.written-review :deep(th),
.written-review :deep(td) {
    border: 1px solid #374151;
    padding: 0.5rem 0.75rem;
    text-align: start;
}

.written-review :deep(th) {
    background: #1f2937;
    color: #f9fafb;
    font-weight: 700;
}

.written-review :deep(hr) {
    border-color: #374151;
    margin-block: 1.25rem;
}
</style>
