def check(cmd, mf):
    m = mf.findNode('pygame')
    if m is None or m.filename is None:
        return None
    return dict(
        packages = ['pygame'],
    )
