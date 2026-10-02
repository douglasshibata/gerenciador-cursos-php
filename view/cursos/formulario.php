<?php include __DIR__ . '/../inicio-html.php'; ?>

    <form action="/salvar-curso<?= isset($curso) ? '?id=' . (int) $curso->getId() : ''; ?>" method="post">
        <div class="form-group">
            <label for="descricao">Descrição</label>
            <input type="text"
                   id="descricao"
                   name="descricao"
                   class="form-control"
                   value="<?= isset($curso) ? htmlspecialchars($curso->getDescricao(), ENT_QUOTES, 'UTF-8') : ''; ?>"
                   required>
        </div>
        <button class="btn btn-primary">Salvar</button>
    </form>

<?php include __DIR__ . '/../fim-html.php'; ?>
