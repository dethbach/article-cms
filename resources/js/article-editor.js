
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import TextAlign from '@tiptap/extension-text-align';
import Link from '@tiptap/extension-link';

document.addEventListener('DOMContentLoaded', () => {
    const editorElement = document.querySelector('#articleEditor');

    if (!editorElement) return;

    const contentInput = document.querySelector('#article-content');
    const articleForm = document.querySelector('#articleForm');
    const toolbar = document.querySelector('#editorToolbar');
    const headingSelect = document.querySelector('#headingSelect');

    const editor = new Editor({
        element: editorElement,

        extensions: [
            StarterKit.configure({
                heading: {
                    levels: [1, 2, 3, 4],
                },
            }),

            TextAlign.configure({
                types: ['heading', 'paragraph'],
                alignments: ['left', 'center', 'right', 'justify'],
                defaultAlignment: 'left',
            }),

            Link.configure({
                openOnClick: false,
                autolink: true,
                HTMLAttributes: {
                    target: '_blank',
                    rel: 'noopener noreferrer',
                },
            }),
        ],

        content: editorElement.dataset.content || '<p></p>',

        editorProps: {
            attributes: {
                class: 'tiptap-content ui-editor__content',
                spellcheck: 'true',
            },
        },

        onUpdate: ({ editor }) => {
            if (contentInput) {
                contentInput.value = editor.getHTML();
            }

            updateToolbar();
        },

        onSelectionUpdate: updateToolbar,
    });

    const commands = {
        paragraph: () => editor.chain().focus().setParagraph().run(),

        h1: () => editor.chain().focus().toggleHeading({ level: 1 }).run(),
        h2: () => editor.chain().focus().toggleHeading({ level: 2 }).run(),
        h3: () => editor.chain().focus().toggleHeading({ level: 3 }).run(),
        h4: () => editor.chain().focus().toggleHeading({ level: 4 }).run(),

        bold: () => editor.chain().focus().toggleBold().run(),
        italic: () => editor.chain().focus().toggleItalic().run(),
        strike: () => editor.chain().focus().toggleStrike().run(),

        bulletList: () => editor.chain().focus().toggleBulletList().run(),
        orderedList: () => editor.chain().focus().toggleOrderedList().run(),
        blockquote: () => editor.chain().focus().toggleBlockquote().run(),

        alignLeft: () => editor.chain().focus().setTextAlign('left').run(),
        alignCenter: () => editor.chain().focus().setTextAlign('center').run(),
        alignRight: () => editor.chain().focus().setTextAlign('right').run(),
        alignJustify: () => editor.chain().focus().setTextAlign('justify').run(),

        undo: () => editor.chain().focus().undo().run(),
        redo: () => editor.chain().focus().redo().run(),

        link: () => {
            const { from, to } = editor.state.selection;

            if (from === to) {
                alert('Please select some text before adding a link.');
                return;
            }

            const previousUrl = editor.getAttributes('link').href || '';
            const url = window.prompt('Enter link URL:', previousUrl);

            if (url === null) return;

            if (url.trim() === '') {
                editor.chain().focus().extendMarkRange('link').unsetLink().run();
                return;
            }

            editor.chain().focus().extendMarkRange('link')
                .setLink({ href: url.trim() })
                .run();
        },

        unlink: () => editor.chain().focus().unsetLink().run(),
    };

    toolbar?.addEventListener('click', (event) => {
        const button = event.target.closest('[data-action]');

        if (!button || button.disabled) return;

        const action = button.dataset.action;

        if (commands[action]) {
            commands[action]();
        }
    });

    headingSelect?.addEventListener('change', (event) => {
        const value = event.target.value;

        if (value === 'paragraph') {
            editor.chain().focus().setParagraph().run();
        } else {
            const level = Number(value.replace('h', ''));

            editor.chain().focus().toggleHeading({ level }).run();
        }
    });

    function updateToolbar() {
        if (!toolbar) return;

        const currentAlignment =
            editor.getAttributes('paragraph').textAlign ||
            editor.getAttributes('heading').textAlign ||
            'left';

        const activeStates = {
            bold: editor.isActive('bold'),
            italic: editor.isActive('italic'),
            strike: editor.isActive('strike'),

            bulletList: editor.isActive('bulletList'),
            orderedList: editor.isActive('orderedList'),
            blockquote: editor.isActive('blockquote'),

            alignLeft: currentAlignment === 'left',
            alignCenter: currentAlignment === 'center',
            alignRight: currentAlignment === 'right',
            alignJustify: currentAlignment === 'justify',

            link: editor.isActive('link'),
        };

        toolbar.querySelectorAll('[data-action]').forEach(button => {
            const action = button.dataset.action;

            button.classList.toggle('active', Boolean(activeStates[action]));

            if (action === 'undo') {
                button.disabled = !editor.can().undo();
            }

            if (action === 'redo') {
                button.disabled = !editor.can().redo();
            }

            if (action === 'unlink') {
                button.disabled = !editor.isActive('link');
            }
        });

        if (headingSelect) {
            if (editor.isActive('heading', { level: 1 })) {
                headingSelect.value = 'h1';
            } else if (editor.isActive('heading', { level: 2 })) {
                headingSelect.value = 'h2';
            } else if (editor.isActive('heading', { level: 3 })) {
                headingSelect.value = 'h3';
            } else if (editor.isActive('heading', { level: 4 })) {
                headingSelect.value = 'h4';
            } else {
                headingSelect.value = 'paragraph';
            }
        }
    }

    articleForm?.addEventListener('submit', () => {
        if (contentInput) {
            contentInput.value = editor.getHTML();
        }
    });

    if (contentInput) {
        contentInput.value = editor.getHTML();
    }

    updateToolbar();
});


