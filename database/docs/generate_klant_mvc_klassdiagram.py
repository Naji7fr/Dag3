"""Generate Klant MVC class diagram draw.io file (Kniploket Tiko)."""

from pathlib import Path
import html
import uuid

OUTPUT = Path(__file__).resolve().parent / "kniploket_klant_mvc_klassdiagram.drawio"

CLASS_STYLE = (
    "swimlane;fontStyle=1;align=center;verticalAlign=top;childLayout=stackLayout;"
    "horizontal=1;startSize=26;horizontalStack=0;resizeParent=1;resizeParentMax=0;"
    "resizeLast=0;collapsible=0;marginBottom=0;whiteSpace=wrap;html=1;"
)
SECTION_STYLE = "text;strokeColor=none;fillColor=none;align=left;verticalAlign=top;spacingLeft=4;spacingRight=4;overflow=hidden;rotatable=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;whiteSpace=wrap;html=1;"
METHOD_STYLE = "text;strokeColor=none;fillColor=none;align=left;verticalAlign=top;spacingLeft=4;spacingRight=4;overflow=hidden;rotatable=0;points=[[0,0.5],[1,0.5]];portConstraint=eastwest;whiteSpace=wrap;html=1;"
EDGE_STYLE = "endArrow=open;endFill=0;html=1;rounded=0;exitX=0.5;exitY=0;exitDx=0;exitDy=0;entryX=0.5;entryY=1;entryDx=0;entryDy=0;"


def uid(prefix: str) -> str:
    return f"{prefix}-{uuid.uuid4().hex[:8]}"


def class_box(cid: str, stereotype: str, name: str, x: int, y: int, w: int, fields: list[str], methods: list[str]) -> list[str]:
    lines = []
    h = 26 + (len(fields) + len(methods)) * 22 + 10
    title = f"{stereotype}&#xa;{name}" if stereotype else name
    lines.append(
        f'        <mxCell id="{cid}" parent="1" value="{html.escape(title)}" style="{CLASS_STYLE}" vertex="1">\n'
        f'          <mxGeometry x="{x}" y="{y}" width="{w}" height="{h}" as="geometry" />\n'
        f"        </mxCell>"
    )
    for i, field in enumerate(fields):
        lines.append(
            f'        <mxCell id="{uid("f")}" parent="{cid}" value="{html.escape(field)}" style="{SECTION_STYLE}" vertex="1">\n'
            f'          <mxGeometry y="{26 + i * 22}" width="{w}" height="22" as="geometry" />\n'
        f"        </mxCell>"
        )
    offset = 26 + len(fields) * 22
    for j, method in enumerate(methods):
        lines.append(
            f'        <mxCell id="{uid("m")}" parent="{cid}" value="{html.escape(method)}" style="{METHOD_STYLE}" vertex="1">\n'
            f'          <mxGeometry y="{offset + j * 22}" width="{w}" height="22" as="geometry" />\n'
        f"        </mxCell>"
        )
    return lines


def main() -> None:
    cells: list[str] = []

    db_id = uid("cls")
    model_id = uid("cls")
    view_id = uid("cls")
    ctrl_id = uid("cls")

    cells += class_box(db_id, "", "Database", 80, 520, 220,
                       ["+ pdo : connection"],
                       ["+ query(sql) : Result"])

    cells += class_box(model_id, "Model", "KlantRepository", 80, 280, 260,
                       ["- Database : db"],
                       ["+ KlantRepository(db : Database)",
                        "+ getAllKlanten(postcode) : list",
                        "+ getKlantById(id) : array",
                        "+ updateKlant(id, data) : bool"])

    cells += class_box(view_id, "View", "KlantView", 420, 280, 240,
                       ["+ List : klanten",
                        "+ klant : record"],
                       ["+ toonOverzicht()",
                        "+ toonDetail()",
                        "+ toonFormulier()"])

    cells += class_box(ctrl_id, "Controller", "KlantController", 220, 40, 280,
                       ["+ KlantRepository : Model",
                        "+ KlantView : View"],
                       ["+ KlantController(model, view)",
                        "+ index()",
                        "+ show()",
                        "+ edit()",
                        "+ update()"])

    edges = [
        (db_id, model_id, "exitX=0.5;exitY=0;entryX=0.5;entryY=1"),
        (model_id, ctrl_id, "exitX=0.5;exitY=0;entryX=0.25;entryY=1"),
        (view_id, ctrl_id, "exitX=0.5;exitY=0;entryX=0.75;entryY=1"),
    ]
    for src, tgt, ports in edges:
        cells.append(
            f'        <mxCell id="{uid("e")}" parent="1" source="{src}" target="{tgt}" '
            f'style="{EDGE_STYLE}{ports};" edge="1">\n'
            f'          <mxGeometry relative="1" as="geometry" />\n'
            f"        </mxCell>"
        )

    title_id = uid("t")
    title = (
        f'        <mxCell id="{title_id}" parent="1" value="Klassendiagram MVC-structuur — Klant (Kniploket Tiko)" '
        f'style="text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;'
        f'whiteSpace=wrap;rounded=0;fontSize=14;fontStyle=1;" vertex="1">\n'
        f'          <mxGeometry x="120" y="0" width="520" height="30" as="geometry" />\n'
        f"        </mxCell>"
    )

    xml = (
        '<mxfile host="app.diagrams.net" agent="Kniploket Tiko" version="26.0.0">\n'
        '  <diagram id="klant-mvc-klassdiagram" name="Klant MVC">\n'
        '    <mxGraphModel dx="1200" dy="800" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" '
        'arrows="1" fold="1" page="1" pageScale="1" pageWidth="827" pageHeight="1169" math="0" shadow="0">\n'
        "      <root>\n"
        '        <mxCell id="0" />\n'
        '        <mxCell id="1" parent="0" />\n'
        f"{title}\n"
        + "\n".join(cells)
        + "\n      </root>\n"
        "    </mxGraphModel>\n"
        "  </diagram>\n"
        "</mxfile>\n"
    )

    OUTPUT.write_text(xml, encoding="utf-8")
    print(f"Saved: {OUTPUT}")


if __name__ == "__main__":
    main()
